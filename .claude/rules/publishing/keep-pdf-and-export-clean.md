---
name: keep-pdf-and-export-clean
description: Every exported PDF states its output target and ships clean, with correct page geometry, embedded fonts, and selectable tagged text produced by deterministic scripts.
paths: ["catalyst/src/Pdf/**", "almanach/**"]
severity: should
---
# Keep PDF and export clean

**Rule:**

- State the output target before you call an export done (screen, accessible, printer/POD, home print). Geometry follows the target: declare `TrimBox`, `BleedBox`, and `MediaBox`, keep live text and folios inside the text-safe area, and add bleed only on edges that bleed. Take the default measurements from `standards/output_production_pdf.md`; do not invent them.
- Ship selectable, tagged text with a logical reading order and a document language. Never rasterize rules text, stat blocks, tables, or page numbers. Give every meaningful image alt text and tag decoration as an artifact.
- Embed or subset every font, and confirm the license permits PDF embedding.
- A scripted exporter is deterministic: the same input produces the same bytes. Resolve paths from the script's own location with `pathlib`, take options through `argparse`, and content-address the output the way `App\Pdf\BookRelease` already computes the release stamp and cache filename. No wall-clock timestamps in filenames or metadata, no current-directory-relative paths, no network at export time.

**Why:** The aubaine-visual-supports-designs skill turns print and accessibility citations into production rules (`standards/output_production_pdf.md`): screen and accessible PDFs need selectable tagged text, a real reading order, and alt text, and a printer PDF needs documented page boxes and embedded fonts. PDF/UA (ISO 14289) is built on tagged PDF, and alt text plus reading order are the same WCAG 2.1 obligations the app already meets (accessibility/always-meet-wcag-aa-and-rgaa). Determinism is what lets the printed cover stamp and the file on disk agree and lets CI diff two runs; a wall-clock name or a cwd-relative path breaks that. The script discipline is the codex lab's discipline (python/keep-scripts-path-relative-and-package-free).

**Good / Bad:**
```python
# Bad: cwd-relative output and a clock-based name. Two runs make two files, and nothing can diff them.
import os, time

out = f"book-{int(time.time())}.pdf"
with open(os.path.join("out", out), "wb") as f:
    f.write(render())
```
```python
# Good: options via argparse, paths from the script, content-addressed name, byte-stable output.
import argparse
from pathlib import Path


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--book", required=True)
    args = parser.parse_args()
    out_dir = Path(__file__).resolve().parent / "out"
    out_dir.mkdir(exist_ok=True)
    # name derived from content, not the clock, so CI can compare runs
    (out_dir / f"{args.book}.pdf").write_bytes(render(args.book))
```

**See also:** [[design-documents-for-the-reading-mode]], [[claim-nothing-you-have-not-verified]], python/keep-scripts-path-relative-and-package-free, accessibility/always-meet-wcag-aa-and-rgaa.

**Enforced by:** ruff (scripted exporters) + the aubaine-visual-supports-designs PDF triage scripts (`standards/output_production_pdf.md`) + review.
