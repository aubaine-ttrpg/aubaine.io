# Output production and PDF standards

This file turns the citations in `references/citations.md` into production rules for TTRPG visual supports across **screen PDFs, accessible PDFs, printer/POD PDFs, home-print PDFs, and source packages**.

## Output target first

Do not say “ready” until the target is known:

- Digital marketplace PDF, screen PDF, accessible PDF, printer/POD PDF, offset PDF, home-print PDF, VTT/reference PDF, or source package.
- Intended device/paper: laptop, tablet, phone, projector, home printer, POD, offset, screen insert, card cutter, binder, or loose leaf.
- Required standard or preset: tagged PDF, PDF/UA goal, PDF/X-4, PDF/X-1a, marketplace limits, platform preset, or plain archival/source PDF.

## Screen PDF

A screen PDF should be pleasant to read and fast to navigate:

- Selectable text; no rasterized rules text.
- Bookmarks that match the real structure.
- Hyperlinked table of contents, page references, index, glossary, and cross-references.
- Correct page labels if front matter uses roman numerals or alternate numbering.
- Useful title/author/subject metadata.
- Reasonable file size without destroying text clarity.
- Pages checked in single-page view and two-page/spread view.
- Links tested after export.

## Accessible PDF

An accessible PDF is not merely a visually clean PDF. Check:

- Tagged PDF structure.
- Logical reading order.
- Document language.
- Title metadata.
- Bookmarks.
- Alt text for meaningful images and diagrams.
- Artifact tagging for decorative items.
- Table headers and cell relationships.
- Form field labels, descriptions, and tab order where forms exist.
- Selectable text, not images of text.

PDF/UA is ISO 14289 and relies on Tagged PDF to carry semantic information; use dedicated validators and manual review before claiming conformance. See citations 16-20.

## Printer/POD PDF

Do not say “print-ready” until the print target is known:

- Print vendor/platform and current file-preparation guide.
- Interior trim size and cover template.
- Binding type: saddle stitch, perfect bound, case bound, spiral, coil, loose-leaf, folded pamphlet, card deck, or screen panels.
- Paper stock and whether the piece is black-and-white, standard color, premium color, offset, risograph, or home print.
- Required PDF standard: platform preset, PDF/X-4, PDF/X-1a, plain PDF, or accessibility-first tagged PDF.

### Page geometry

Document all of these in the production notes:

- `TrimBox` - final cut size.
- `BleedBox` - artwork area beyond trim.
- `MediaBox` - total PDF page canvas.
- Safe area - conceptual zone where live text, folios, rules, tables, and icons must remain.
- Gutter/binding safety - extra inside margin for perfect-bound, coil, spiral, punched, or folded pages.

Default if no printer is specified:

- Bleed: `0.125 in / 3.18 mm` on edges that bleed.
- Text safety: `0.5 in / 12.7 mm` minimum.
- No crop, registration, or printer marks for POD unless the platform asks for them.

DriveThruRPG’s quick print specs call for 0.125 in bleed on outside non-binding edges, no crop/printer/registration marks, and text at least 0.5 in from the page edge. Lulu’s current bleed guidance uses 0.125 in / 3.18 mm past trim. See citations 8-10.

### Color and CMYK

- Use the printer’s requested ICC profile/output intent. PDF/X requires an output intent that describes the intended print condition; see citations 1, 2, and 7.
- Prefer PDF/X-4 for modern print workflows unless the printer requires PDF/X-1a or a platform preset. Ghent Workgroup’s modern commercial-print specifications are PDF/X-4 based; see citation 3.
- Convert RGB art intentionally, not accidentally. Keep a source RGB master and a print export using the printer profile.
- Body text should usually be 100K black, not rich black, to avoid registration fuzz.
- Large black solids may use printer-approved rich black; never invent total ink coverage limits without the print profile.
- Check overprint and knockout behavior on small white type, rules, and icons.
- Avoid low-contrast color combinations in print even if they pass on a monitor; paper, dot gain, texture, and lighting reduce apparent contrast.

### Fonts and text

- Use licensed fonts that allow PDF embedding and the intended commercial/digital use.
- Embed fonts or subsets in the PDF unless the printer explicitly requires outlining.
- Never rasterize body text, stat blocks, tables, form fields, page numbers, or map keys when the toolchain can preserve live text.
- Avoid hairline type, tiny all-caps, distressed fonts for rules text, and decorative faces in tables.
- Use OpenType features intentionally; verify small caps, tabular figures, fractions, and ligatures in final PDF renders.

### Raster/vector assets

This skill is not image generation, but visual supports often contain art, icons, maps, borders, rules diagrams, or textures.

- Raster images: 300 ppi at final printed size unless the printer requests otherwise.
- Line art, icons, logos, maps, and rules diagrams: vector where possible.
- Do not place critical rules or numbers only in raster images.
- Keep map labels, room keys, page references, and card text as live text whenever possible.
- Flatten transparency only if the printer or PDF/X-1a workflow requires it. PDF/X-4 supports live transparency in modern workflows.

## Home-print PDF

Home print is a distinct target, not a weaker printer PDF:

- Low ink coverage.
- Grayscale-safe design.
- No essential full-bleed elements.
- Wide enough margins for imperfect printers.
- Cut/fold instructions when relevant.
- Large enough form fields for pencil/pen.
- Optional imposed booklet file only if clearly marked; keep reader-order PDF too.

## Source package

A professional source package includes:

- Manuscript/source text.
- Layout source file(s) where legally shareable.
- Linked assets or asset manifest.
- Fonts list and license notes, not necessarily font files.
- Style guide.
- Rights ledger.
- Export preset notes.
- Production notes.
- Version/changelog/errata URL.
- Final PDFs and proof images if useful.

Never redistribute font files unless the license explicitly permits it.

## Visual QA checklist

Render and inspect the final PDF before delivery:

- Page size and page boxes are consistent.
- No missing glyphs, black boxes, font substitution, broken ligatures, or clipped descenders.
- No text inside trim, fold, binding, punch, or panel-seam hazard zones.
- Full-bleed elements extend into bleed where required.
- Page numbers and running heads do not collide with illustrations, tables, forms, or maps.
- Tables do not break across pages without repeated headers.
- Spread-critical art and labels are not swallowed by the gutter.
- Small reversed text remains readable.
- Rich black, overprint, knockout, transparency, and tint choices are intentional.
- All links, bookmarks, form fields, and page labels work in digital editions.

## Compliance warning

The included scripts are triage tools. Do not claim formal PDF/X, PDF/UA, accessibility, legal, or printer conformance unless the right validators and manual reviews have been completed. Only the printer/platform can approve final print acceptance.
