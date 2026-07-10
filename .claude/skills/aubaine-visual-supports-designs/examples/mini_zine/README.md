# Mini zine example

This sample demonstrates the skill as a small visual support package: a zine-like adventure that can produce a screen PDF, accessible PDF, and optional booklet/home-print export.

Run from the package root:

```bash
python scripts/manuscript_lint.py examples/mini_zine/manuscript.md
python scripts/check_contrast.py templates/style_guide.yml
python scripts/signature_planner.py 22 --signature-size 4
```
