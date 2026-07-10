# Workflow: final output preflight

## 1. Before export

Confirm the exact output target:

- Screen PDF.
- Accessible PDF.
- Printer/POD PDF.
- Home-print PDF.
- Cards/sheets/handouts.
- GM-screen panels.
- Source package.

Then confirm the target-specific requirements:

- Digital: bookmarks, hyperlinks, page labels, metadata, file size, selectable text.
- Accessibility: tags, reading order, document language, alt text, table headers, form labels, tab order.
- Print/POD: trim, bleed, safe area, binding, page count, fonts, image resolution, output intent, printer preset.
- Home print: low ink, grayscale safety, margins, cut/fold instructions.
- Source package: rights ledger, style guide, production notes, export settings, asset manifest.

## 2. Export deliberately

Create separate exports when needed:

- Screen PDF.
- Accessible PDF.
- Printer PDF.
- Home-print PDF.
- Proof PDF with comments/crop marks only if requested.
- Source package ZIP.

Never send an imposed booklet PDF as the only reader copy unless the user explicitly requested only imposed print files.

## 3. Script triage

```bash
python scripts/check_contrast.py templates/style_guide.yml
python scripts/manuscript_lint.py path/to/manuscript.md
python scripts/export_preflight.py path/to/export.pdf --expected-page-size 6.25x9.25in
```

Use `--expected-page-size` for print/POD exports or fixed-size sheets/cards. Omit it for variable or not-yet-final digital PDFs.

## 4. Visual review

Render pages to PNGs using a trusted PDF renderer and inspect:

- First page and last page.
- Front matter.
- Table of contents.
- Every chapter opener.
- Every table crossing a page boundary.
- Every stat block template.
- Every handout/sheet/card.
- Every GM-screen panel.
- Any page with reversed type.
- Any page with transparency, tint, or texture behind text.
- Any full-bleed or trim-sensitive page.
- Any link-heavy or bookmark-heavy section.

## 5. Manual sign-off

Do not claim compliance unless verified by the right tool:

- PDF/X: use dedicated PDF/X preflight/validator.
- PDF/UA: use accessibility checker plus manual structure/reading-order review.
- Accessibility: combine automated checks with manual review and assistive-technology testing where possible.
- Printer approval: only the printer/platform can approve final acceptance.
- Legal clearance: only actual rights records and license text can support the claim.
