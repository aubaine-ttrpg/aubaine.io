"""Shared helpers for rendering lab results into committed ``data/`` files.

Markdown tables are embedded into the rule prose; JSON is the machine-readable source other
domains' labs read. Outputs are deterministic so a regenerate-and-compare test can guard drift.
"""

from __future__ import annotations

import json
from pathlib import Path


def pct(x: float, places: int = 1) -> str:
    return f"{x * 100:.{places}f}%"


def one_in(x: float) -> str:
    if x <= 0:
        return "never"
    return f"1 in {round(1 / x):,}"


def signed(n: int) -> str:
    return f"+{n}" if n >= 0 else str(n)


def markdown_table(headers: list[str], rows: list[list[object]]) -> str:
    head = "| " + " | ".join(headers) + " |"
    sep = "| " + " | ".join("---" for _ in headers) + " |"
    body = [f"| {' | '.join(str(c) for c in row)} |" for row in rows]
    return "\n".join([head, sep, *body]) + "\n"


def write_text(path: Path, text: str) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(text, encoding="utf-8")


def write_json(path: Path, obj: object) -> None:
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(json.dumps(obj, indent=2) + "\n", encoding="utf-8")
