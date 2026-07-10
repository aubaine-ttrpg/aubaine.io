# Workflow: screen-readable and accessible PDFs

## 1. Define the digital job

Decide whether the PDF is primarily:

- A beautiful reading PDF.
- A tablet/laptop reference PDF.
- An accessible PDF.
- A printer-friendly/home-print PDF.
- A marketplace preview PDF.
- A combined edition with compromises clearly documented.

Do not assume one export can serve every job equally well.

## 2. Navigation architecture

Before layout export, verify:

- Bookmarks mirror the real heading structure.
- Table of contents links work.
- Cross-references and page references work.
- Glossary/index entries use stable terminology.
- Page labels match front matter/body numbering.
- File metadata contains title and author/studio.

## 3. Reading order and tags

For accessible editions:

- Export tagged PDF where the layout tool supports it.
- Check reading order manually.
- Mark decorative items as artifacts.
- Add alt text for meaningful images, maps, diagrams, and icons.
- Use table headers and avoid fake tables made from spaces/tabs.
- Label form fields and set logical tab order.
- Set document language.

## 4. Visual readability

- Check body text at common zoom levels.
- Check pages in single-page and two-page view.
- Avoid tiny marginalia for digital-first PDFs.
- Ensure important color cues have labels/icons/shapes.
- Confirm line art, rules, borders, and tints do not shimmer or disappear on screen.

## 5. Export set

Recommended digital exports:

- `screen.pdf` - normal reading, bookmarks, links, searchable text.
- `accessible.pdf` - tagged/structured and manually reviewed.
- `printer_friendly.pdf` - low ink, grayscale-safe, wide margins.
- `print_pod.pdf` - only when an actual print target is known.

## 6. Final checks

```bash
python scripts/check_contrast.py templates/style_guide.yml
python scripts/manuscript_lint.py path/to/manuscript.md
python scripts/export_preflight.py path/to/screen.pdf
```

Then manually check links, bookmarks, search terms, page labels, reading order, and file size.
