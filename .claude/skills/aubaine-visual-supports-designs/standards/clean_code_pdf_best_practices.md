# Clean code and PDF scripting best practices

## Code principles

- Scripts should be deterministic: same inputs, same outputs.
- Use `pathlib.Path` for filesystem paths.
- Use `argparse` for command-line interfaces.
- Validate inputs early and exit with clear messages.
- Write machine-readable output when useful (`--json`) and human-readable output by default.
- Do not silently overwrite final deliverables unless the user requested it.
- Keep optional dependencies optional and explain degraded checks.
- Avoid network calls in production preflight scripts; use local files and documented printer specs.
- Prefer small composable scripts over a single opaque generator.
- Follow PEP 8 style expectations; see citations 33-35.

## PDF scripting principles

- Never trust PDF text extraction as the only layout verification. Render pages and inspect the PNGs.
- Treat PDF points as 1/72 inch and convert units explicitly.
- Record expected trim/bleed/page size in production notes and script arguments.
- Use external tools when available: `pdfinfo`, `pdffonts`, `qpdf`, Ghostscript, veraPDF, callas/pdfToolbox, or platform-specific preflight.
- Do not claim PDF/X or PDF/UA conformance from a simple script. Automated checks can flag issues; formal conformance requires dedicated validators and manual review.
- Preserve source files. A final PDF is not a layout source.

## Preflight script limitations

The included `export_preflight.py` is a triage tool. It can inspect page sizes, page boxes, fonts via PyMuPDF and/or `pdffonts`, and image resolution hints. It cannot guarantee:

- PDF/X conformance.
- PDF/UA conformance.
- Correct ICC output intent.
- Total ink coverage.
- Overprint correctness.
- Printer acceptance.

Use it to catch common mistakes before professional preflight.
