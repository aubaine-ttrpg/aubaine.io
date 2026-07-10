#!/usr/bin/env python3
"""Triage a PDF export for common production issues.

This is not a formal PDF/X, PDF/UA, accessibility, color, or printer
conformance validator. It checks page size consistency, page boxes, font
diagnostics, and image resolution hints. Use dedicated preflight tools,
accessibility checks, manual proofing, and printer/platform upload checks
before calling an output approved.
"""

from __future__ import annotations

import argparse
import json
import re
import shutil
import subprocess
import sys
from dataclasses import asdict, dataclass
from pathlib import Path
from typing import Any

POINTS_PER_INCH = 72.0
MM_PER_INCH = 25.4
SIZE_RE = re.compile(r"^\s*([0-9.]+)x([0-9.]+)\s*(in|mm|pt)?\s*$", re.I)

try:
    import fitz  # PyMuPDF
except ImportError:  # pragma: no cover - environment-specific
    fitz = None  # type: ignore[assignment]


@dataclass(frozen=True)
class Finding:
    severity: str
    code: str
    message: str
    page: int | None = None


@dataclass(frozen=True)
class PageInfo:
    page: int
    width_pt: float
    height_pt: float
    width_in: float
    height_in: float
    rotation: int


@dataclass(frozen=True)
class FontInfo:
    page: int | None
    name: str
    type: str
    embedded: str


def parse_size(value: str) -> tuple[float, float]:
    """Parse WxH with unit in/mm/pt and return points."""
    match = SIZE_RE.match(value)
    if not match:
        raise ValueError("Expected size like 6.25x9.25in, 148x210mm, or 450x650pt")
    width = float(match.group(1))
    height = float(match.group(2))
    unit = (match.group(3) or "in").lower()
    if unit == "in":
        return width * POINTS_PER_INCH, height * POINTS_PER_INCH
    if unit == "mm":
        return width / MM_PER_INCH * POINTS_PER_INCH, height / MM_PER_INCH * POINTS_PER_INCH
    if unit == "pt":
        return width, height
    raise ValueError(f"Unsupported unit: {unit}")


def pt_to_in(value: float) -> float:
    return value / POINTS_PER_INCH


def nearly_equal(left: float, right: float, tolerance: float = 0.75) -> bool:
    return abs(left - right) <= tolerance


def run_command(command: list[str]) -> str | None:
    if not shutil.which(command[0]):
        return None
    try:
        completed = subprocess.run(
            command,
            check=False,
            stdout=subprocess.PIPE,
            stderr=subprocess.STDOUT,
            text=True,
            timeout=30,
        )
    except (OSError, subprocess.SubprocessError):
        return None
    return completed.stdout


def inspect_with_fitz(pdf_path: Path) -> tuple[list[PageInfo], list[Finding], list[FontInfo], dict[str, Any]]:
    if fitz is None:
        raise RuntimeError("PyMuPDF is required for built-in PDF inspection. Install with: pip install pymupdf")

    findings: list[Finding] = []
    pages: list[PageInfo] = []
    fonts: list[FontInfo] = []
    extras: dict[str, Any] = {"image_warnings": []}

    document = fitz.open(pdf_path)
    try:
        for page_index in range(document.page_count):
            page = document.load_page(page_index)
            rect = page.rect
            pages.append(
                PageInfo(
                    page=page_index + 1,
                    width_pt=round(rect.width, 3),
                    height_pt=round(rect.height, 3),
                    width_in=round(pt_to_in(rect.width), 3),
                    height_in=round(pt_to_in(rect.height), 3),
                    rotation=page.rotation,
                )
            )
            if page.rotation not in (0, 90, 180, 270):
                findings.append(
                    Finding("warning", "unusual_rotation", f"Unusual page rotation {page.rotation}", page_index + 1)
                )

            for font in page.get_fonts(full=True):
                # PyMuPDF tuple shape can vary. Common fields: xref, ext, type, basefont, name, encoding, referencer.
                font_type = str(font[2]) if len(font) > 2 else "unknown"
                base_name = str(font[3]) if len(font) > 3 else str(font)
                embedded_hint = "unknown-use-pdffonts"
                fonts.append(FontInfo(page=page_index + 1, name=base_name, type=font_type, embedded=embedded_hint))

            for info in page.get_image_info(xrefs=True):
                bbox = info.get("bbox")
                width_px = info.get("width")
                height_px = info.get("height")
                if not bbox or not width_px or not height_px:
                    continue
                bbox_width_pt = max(float(bbox[2]) - float(bbox[0]), 0.01)
                bbox_height_pt = max(float(bbox[3]) - float(bbox[1]), 0.01)
                ppi_x = float(width_px) / pt_to_in(bbox_width_pt)
                ppi_y = float(height_px) / pt_to_in(bbox_height_pt)
                if min(ppi_x, ppi_y) < 250:
                    extras["image_warnings"].append(
                        {
                            "page": page_index + 1,
                            "ppi_x": round(ppi_x, 1),
                            "ppi_y": round(ppi_y, 1),
                            "message": "Raster image appears below 250 ppi at placed size; verify intentionally.",
                        }
                    )
    finally:
        document.close()
    return pages, findings, fonts, extras


def parse_pdffonts(output: str) -> list[FontInfo]:
    lines = [line for line in output.splitlines() if line.strip()]
    if len(lines) < 3:
        return []
    fonts: list[FontInfo] = []
    for line in lines[2:]:
        # pdffonts columns are fixed-ish: name type encoding emb sub uni object ID
        parts = line.split()
        if len(parts) < 5:
            continue
        embedded = parts[-4] if len(parts) >= 4 else "unknown"
        font_type = " ".join(parts[1:-5]) if len(parts) > 6 else "unknown"
        fonts.append(FontInfo(page=None, name=parts[0], type=font_type, embedded=embedded))
    return fonts


def inspect_external_tools(pdf_path: Path) -> dict[str, Any]:
    diagnostics: dict[str, Any] = {}
    pdfinfo = run_command(["pdfinfo", str(pdf_path)])
    if pdfinfo is not None:
        diagnostics["pdfinfo"] = pdfinfo
    pdffonts = run_command(["pdffonts", str(pdf_path)])
    if pdffonts is not None:
        diagnostics["pdffonts"] = pdffonts
        diagnostics["pdffonts_parsed"] = [asdict(font) for font in parse_pdffonts(pdffonts)]
    qpdf = run_command(["qpdf", "--check", str(pdf_path)])
    if qpdf is not None:
        diagnostics["qpdf_check"] = qpdf
    return diagnostics


def build_findings(
    pages: list[PageInfo],
    expected_page_size: tuple[float, float] | None,
    diagnostics: dict[str, Any],
    extras: dict[str, Any],
) -> list[Finding]:
    findings: list[Finding] = []
    if not pages:
        findings.append(Finding("error", "no_pages", "No pages found."))
        return findings

    first = pages[0]
    for page in pages[1:]:
        if not (nearly_equal(page.width_pt, first.width_pt) and nearly_equal(page.height_pt, first.height_pt)):
            findings.append(
                Finding(
                    "warning",
                    "mixed_page_sizes",
                    (
                        f"Page size differs from page 1: {page.width_in:.3f}x{page.height_in:.3f} in "
                        f"vs {first.width_in:.3f}x{first.height_in:.3f} in."
                    ),
                    page.page,
                )
            )

    if expected_page_size:
        expected_w, expected_h = expected_page_size
        for page in pages:
            same_orientation = nearly_equal(page.width_pt, expected_w) and nearly_equal(page.height_pt, expected_h)
            swapped_orientation = nearly_equal(page.width_pt, expected_h) and nearly_equal(page.height_pt, expected_w)
            if not (same_orientation or swapped_orientation):
                findings.append(
                    Finding(
                        "error",
                        "unexpected_page_size",
                        (
                            f"Expected about {pt_to_in(expected_w):.3f}x{pt_to_in(expected_h):.3f} in; "
                            f"found {page.width_in:.3f}x{page.height_in:.3f} in."
                        ),
                        page.page,
                    )
                )

    parsed_fonts = diagnostics.get("pdffonts_parsed", [])
    for font in parsed_fonts:
        embedded = str(font.get("embedded", "")).lower()
        if embedded in {"no", "false", "n"}:
            findings.append(
                Finding("error", "font_not_embedded", f"Font may not be embedded: {font.get('name')}")
            )

    for warning in extras.get("image_warnings", []):
        findings.append(
            Finding(
                "warning",
                "low_image_ppi",
                warning["message"] + f" ({warning['ppi_x']}x{warning['ppi_y']} ppi)",
                int(warning["page"]),
            )
        )

    qpdf_check = diagnostics.get("qpdf_check", "")
    if qpdf_check and "No syntax or stream encoding errors" not in qpdf_check:
        findings.append(Finding("warning", "qpdf_check", "qpdf reported diagnostics; inspect output."))

    return findings


def print_human(report: dict[str, Any]) -> None:
    print(f"PDF: {report['path']}")
    print(f"Pages: {len(report['pages'])}")
    if report["pages"]:
        first = report["pages"][0]
        print(f"Page 1 size: {first['width_in']} x {first['height_in']} in")
    print()

    findings = report["findings"]
    if findings:
        for finding in findings:
            page = f" page {finding['page']}" if finding.get("page") else ""
            print(f"{finding['severity'].upper():7} {finding['code']}{page}: {finding['message']}")
    else:
        print("No built-in findings. Manual proofing and formal preflight still required.")

    print("\nExternal tools detected:")
    for tool in ("pdfinfo", "pdffonts", "qpdf_check"):
        print(f"- {tool}: {'yes' if tool in report['diagnostics'] else 'no'}")


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("pdf", type=Path, help="PDF to inspect")
    parser.add_argument(
        "--expected-page-size",
        help="Expected exported page size, including bleed when relevant, e.g. 6.25x9.25in or 154x216mm",
    )
    parser.add_argument("--json", action="store_true", help="Write JSON output")
    args = parser.parse_args(argv)

    if not args.pdf.exists():
        print(f"ERROR: not found: {args.pdf}", file=sys.stderr)
        return 2

    try:
        expected = parse_size(args.expected_page_size) if args.expected_page_size else None
        pages, initial_findings, fonts, extras = inspect_with_fitz(args.pdf)
        diagnostics = inspect_external_tools(args.pdf)
        findings = initial_findings + build_findings(pages, expected, diagnostics, extras)
    except Exception as exc:  # noqa: BLE001 - CLI should show a clean error
        print(f"ERROR: {exc}", file=sys.stderr)
        return 2

    report = {
        "path": str(args.pdf),
        "pages": [asdict(page) for page in pages],
        "fonts_seen_by_pymupdf": [asdict(font) for font in fonts],
        "diagnostics": diagnostics,
        "extras": extras,
        "findings": [asdict(finding) for finding in findings],
        "limitations": [
            "Not a formal PDF/X validator.",
            "Not a formal PDF/UA validator.",
            "Does not validate ICC output intent or total ink coverage.",
            "Printer/platform approval still required.",
        ],
    }

    if args.json:
        print(json.dumps(report, indent=2))
    else:
        print_human(report)

    return 1 if any(finding.severity == "error" for finding in findings) else 0


if __name__ == "__main__":
    raise SystemExit(main())
