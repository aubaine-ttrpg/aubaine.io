#!/usr/bin/env python3
"""Check WCAG contrast pairs in a TTRPG visual support style guide.

The style guide should be YAML and contain a top-level `contrast_pairs` list:

contrast_pairs:
  - name: body on paper
    foreground: "#171310"
    background: "#F7F1E3"
    min_ratio: 4.5

This script checks RGB/screen-preview contrast. Print contrast still needs
physical proofing because paper, ink, lighting, dot gain, and color management
change perceived contrast. Passing contrast here is a design gate, not a full
accessibility or print-proof certification.
"""

from __future__ import annotations

import argparse
import json
import re
import sys
from dataclasses import asdict, dataclass
from pathlib import Path
from typing import Any

try:
    import yaml
except ImportError:  # pragma: no cover - environment-specific
    yaml = None  # type: ignore[assignment]

HEX_RE = re.compile(r"^#?([0-9a-fA-F]{6})$")


@dataclass(frozen=True)
class ContrastResult:
    name: str
    foreground: str
    background: str
    ratio: float
    min_ratio: float
    passes: bool


def parse_hex_color(value: str) -> tuple[int, int, int]:
    """Parse #RRGGBB and return an RGB tuple."""
    match = HEX_RE.match(value.strip())
    if not match:
        raise ValueError(f"Invalid hex color: {value!r}; expected #RRGGBB")
    hex_value = match.group(1)
    return tuple(int(hex_value[i : i + 2], 16) for i in (0, 2, 4))  # type: ignore[return-value]


def _linear_channel(channel: int) -> float:
    srgb = channel / 255.0
    if srgb <= 0.04045:
        return srgb / 12.92
    return ((srgb + 0.055) / 1.055) ** 2.4


def relative_luminance(rgb: tuple[int, int, int]) -> float:
    """Return WCAG relative luminance for an RGB color."""
    red, green, blue = (_linear_channel(channel) for channel in rgb)
    return 0.2126 * red + 0.7152 * green + 0.0722 * blue


def contrast_ratio(foreground: str, background: str) -> float:
    """Return WCAG contrast ratio for two hex colors."""
    lum1 = relative_luminance(parse_hex_color(foreground))
    lum2 = relative_luminance(parse_hex_color(background))
    lighter = max(lum1, lum2)
    darker = min(lum1, lum2)
    return (lighter + 0.05) / (darker + 0.05)


def load_yaml(path: Path) -> dict[str, Any]:
    if yaml is None:
        raise RuntimeError("PyYAML is required. Install with: pip install pyyaml")
    with path.open("r", encoding="utf-8") as handle:
        data = yaml.safe_load(handle) or {}
    if not isinstance(data, dict):
        raise ValueError(f"Expected a YAML mapping in {path}")
    return data


def check_pairs(data: dict[str, Any]) -> list[ContrastResult]:
    raw_pairs = data.get("contrast_pairs", [])
    if not isinstance(raw_pairs, list):
        raise ValueError("`contrast_pairs` must be a list")

    results: list[ContrastResult] = []
    for index, pair in enumerate(raw_pairs, start=1):
        if not isinstance(pair, dict):
            raise ValueError(f"contrast_pairs[{index}] must be a mapping")
        name = str(pair.get("name") or f"pair {index}")
        foreground = str(pair.get("foreground", ""))
        background = str(pair.get("background", ""))
        min_ratio = float(pair.get("min_ratio", 4.5))
        ratio = contrast_ratio(foreground, background)
        results.append(
            ContrastResult(
                name=name,
                foreground=foreground,
                background=background,
                ratio=round(ratio, 2),
                min_ratio=min_ratio,
                passes=ratio >= min_ratio,
            )
        )
    return results


def print_text(results: list[ContrastResult]) -> None:
    if not results:
        print("No contrast pairs found.")
        return
    for result in results:
        status = "PASS" if result.passes else "FAIL"
        print(
            f"{status:4}  {result.name}: {result.ratio:.2f}:1 "
            f"(min {result.min_ratio:.2f}:1) "
            f"fg={result.foreground} bg={result.background}"
        )


def main(argv: list[str] | None = None) -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("style_guide", type=Path, help="Path to style guide YAML")
    parser.add_argument("--json", action="store_true", help="Write JSON output")
    args = parser.parse_args(argv)

    try:
        data = load_yaml(args.style_guide)
        results = check_pairs(data)
    except Exception as exc:  # noqa: BLE001 - CLI should show a clean error
        print(f"ERROR: {exc}", file=sys.stderr)
        return 2

    if args.json:
        print(json.dumps([asdict(result) for result in results], indent=2))
    else:
        print_text(results)

    return 0 if all(result.passes for result in results) else 1


if __name__ == "__main__":
    raise SystemExit(main())
