#!/usr/bin/env python3
"""Lint tabletop RPG manuscripts for AI tells and table-use problems.

The goal is editorial triage, not censorship. Every finding should be reviewed
by a human editor in context.
"""

from __future__ import annotations

import argparse
import json
import re
import sys
from dataclasses import asdict, dataclass
from pathlib import Path
from typing import Iterable

AI_TELL_PATTERNS = {
    "meta_ai": re.compile(r"\b(as an ai|ai-generated|language model|i cannot assist)\b", re.I),
    "generic_grandiosity": re.compile(
        r"\b(delve|tapestry|embark|immersive experience|richly detailed|"
        r"dynamic and engaging|seamlessly blends|unleash your imagination|"
        r"whether you(?:'re| are) a seasoned|from novice to veteran)\b",
        re.I,
    ),
    "unsupported_claim": re.compile(
        r"\b(fully accessible|print-ready|printer-safe|professionally balanced|"
        r"extensively playtested|award-winning|industry standard)\b",
        re.I,
    ),
    "placeholder": re.compile(r"\b(TODO|FIXME|lorem ipsum|tk\b|\[insert|\[todo)\b", re.I),
    "generic_fantasy_cluster": re.compile(
        r"\b(ancient secrets|forgotten realm|echoes of|whispers of|shadowy figure|"
        r"dark forces|fate of the world|hidden destiny)\b",
        re.I,
    ),
}

RULE_TRIGGER_RE = re.compile(r"\b(when|if|after|before|on a hit|on a miss|roll|test|check|mark|spend)\b", re.I)
DICE_TABLE_RE = re.compile(r"\b(?:d\d+|\d+d\d+)\b", re.I)
HEADING_RE = re.compile(r"^(#{1,6})\s+(.+?)\s*$")


@dataclass(frozen=True)
class Finding:
    path: str
    line: int
    severity: str
    code: str
    message: str
    excerpt: str


def iter_lines(path: Path) -> Iterable[tuple[int, str]]:
    with path.open("r", encoding="utf-8") as handle:
        for line_number, line in enumerate(handle, start=1):
            yield line_number, line.rstrip("\n")


def lint_text(path: Path, text: str) -> list[Finding]:
    findings: list[Finding] = []
    lines = text.splitlines()

    for line_number, line in enumerate(lines, start=1):
        for code, pattern in AI_TELL_PATTERNS.items():
            if pattern.search(line):
                findings.append(
                    Finding(
                        path=str(path),
                        line=line_number,
                        severity="warning" if code != "placeholder" else "error",
                        code=code,
                        message=f"Review possible {code.replace('_', ' ')}.",
                        excerpt=line.strip()[:220],
                    )
                )

    findings.extend(check_heading_order(path, lines))
    findings.extend(check_front_matter_signals(path, text))
    findings.extend(check_ttrpg_usefulness(path, lines))
    return findings


def check_heading_order(path: Path, lines: list[str]) -> list[Finding]:
    findings: list[Finding] = []
    last_level = 0
    for line_number, line in enumerate(lines, start=1):
        match = HEADING_RE.match(line)
        if not match:
            continue
        level = len(match.group(1))
        if last_level and level > last_level + 1:
            findings.append(
                Finding(
                    path=str(path),
                    line=line_number,
                    severity="warning",
                    code="heading_jump",
                    message=f"Heading jumps from H{last_level} to H{level}; check hierarchy.",
                    excerpt=line.strip(),
                )
            )
        last_level = level
    return findings


def check_front_matter_signals(path: Path, text: str) -> list[Finding]:
    findings: list[Finding] = []
    lowered = text.lower()
    expected_terms = {
        "credits/copyright": ("copyright", "credits"),
        "license/attribution": ("license", "attribution"),
        "table of contents or use path": ("table of contents", "how to use"),
        "accessibility note": ("accessibility", "accessible"),
    }
    for label, terms in expected_terms.items():
        if not any(term in lowered for term in terms):
            findings.append(
                Finding(
                    path=str(path),
                    line=1,
                    severity="info",
                    code="missing_front_matter_signal",
                    message=f"No obvious {label} found; confirm front matter is handled elsewhere.",
                    excerpt="",
                )
            )
    return findings


def check_ttrpg_usefulness(path: Path, lines: list[str]) -> list[Finding]:
    findings: list[Finding] = []
    current_heading = ""
    paragraph: list[tuple[int, str]] = []

    def flush_paragraph() -> None:
        if not paragraph:
            return
        start_line = paragraph[0][0]
        text = " ".join(piece for _, piece in paragraph).strip()
        word_count = len(text.split())
        in_lore_section = any(
            word in current_heading.lower()
            for word in ("lore", "setting", "history", "world", "background")
        )
        if word_count >= 120 and in_lore_section and not RULE_TRIGGER_RE.search(text):
            findings.append(
                Finding(
                    path=str(path),
                    line=start_line,
                    severity="info",
                    code="lore_wall",
                    message=(
                        "Long lore/background paragraph has no obvious table procedure. "
                        "Consider rumors, faction moves, clocks, or keyed details."
                    ),
                    excerpt=text[:220],
                )
            )
        if word_count >= 80 and "rule" in current_heading.lower() and not RULE_TRIGGER_RE.search(text):
            findings.append(
                Finding(
                    path=str(path),
                    line=start_line,
                    severity="warning",
                    code="rule_without_trigger",
                    message="Rules section paragraph lacks obvious trigger/action words.",
                    excerpt=text[:220],
                )
            )
        paragraph.clear()

    for line_number, line in enumerate(lines, start=1):
        heading_match = HEADING_RE.match(line)
        if heading_match:
            flush_paragraph()
            current_heading = heading_match.group(2)
            continue
        if not line.strip():
            flush_paragraph()
            continue
        paragraph.append((line_number, line.strip()))

        if "|" in line and DICE_TABLE_RE.search(line) and "result" not in line.lower():
            findings.append(
                Finding(
                    path=str(path),
                    line=line_number,
                    severity="info",
                    code="table_header_check",
                    message="Dice table line found; ensure header says die/range/result/outcome clearly.",
                    excerpt=line.strip()[:220],
                )
            )
    flush_paragraph()
    return findings


def collect_files(paths: list[Path]) -> list[Path]:
    files: list[Path] = []
    for path in paths:
        if path.is_dir():
            files.extend(sorted(path.rglob("*.md")))
            files.extend(sorted(path.rglob("*.txt")))
        else:
            files.append(path)
    return files


def print_text(findings: list[Finding]) -> None:
    if not findings:
        print("No findings.")
        return
    for finding in findings:
        location = f"{finding.path}:{finding.line}"
        print(f"{finding.severity.upper():7} {finding.code:28} {location}")
        print(f"        {finding.message}")
        if finding.excerpt:
            print(f"        {finding.excerpt}")


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("paths", nargs="+", type=Path, help="Markdown/text files or directories")
    parser.add_argument("--json", action="store_true", help="Write JSON output")
    args = parser.parse_args(argv)

    findings: list[Finding] = []
    for path in collect_files(args.paths):
        if not path.exists():
            print(f"ERROR: not found: {path}", file=sys.stderr)
            return 2
        try:
            text = path.read_text(encoding="utf-8")
        except UnicodeDecodeError as exc:
            print(f"ERROR: cannot read {path}: {exc}", file=sys.stderr)
            return 2
        findings.extend(lint_text(path, text))

    if args.json:
        print(json.dumps([asdict(finding) for finding in findings], indent=2))
    else:
        print_text(findings)

    return 1 if any(finding.severity == "error" for finding in findings) else 0


if __name__ == "__main__":
    raise SystemExit(main())
