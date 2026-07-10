# Aubaine Visual Supports Designs Skill

A reusable skill package for designing polished tabletop RPG **visual supports**: books, booklets, zines, adventure modules, rulebooks, quickstarts, screen PDFs, accessible PDFs, sheets, GM screens, reference cards, inserts, trackers, forms, handouts, and other table-facing publication artifacts.

This is deliberately **not** an image-only or artwork-generation skill. It focuses on the designed support around play: layout, typography, information hierarchy, front matter, tables, sheets, PDF/export hygiene, accessibility, legal/credit hygiene, and editorial polish that avoids obvious AI-generated tells.

## Quick start

1. Copy `templates/project_brief.yml` into a new project folder and fill the artifact and output details.
2. Draft or paste manuscript content using `templates/content_outline.md`.
3. Apply the relevant workflow from `workflows/`.
4. Use `templates/style_guide.yml` to define type, layout, color, icon, and contrast decisions.
5. Run the scripts before final export:

```bash
python scripts/check_contrast.py templates/style_guide.yml
python scripts/manuscript_lint.py examples/mini_zine/manuscript.md
python scripts/signature_planner.py 36 --signature-size 16
python scripts/export_preflight.py final.pdf --expected-page-size 6.25x9.25in
```

## What “visual support” means here

A visual support is any designed object that helps tabletop play happen: a book, PDF, table, sheet, tracker, handout, screen panel, card, quick reference, rules summary, front matter package, or printable/digital component. It may contain artwork, but artwork is not the point. The point is readable, searchable, usable, credited, exportable structure.

## What “output-ready” means

Output-ready means the artifact has a declared target and has been checked against that target:

- Screen PDF: bookmarks, hyperlinks, searchable text, metadata, usable file size, page labels, and navigation.
- Accessible PDF: tagged structure, correct reading order, document language, alt text, table headers, form labels, and contrast.
- Printer/POD PDF: trim, bleed, safe area, margins/gutter, color mode assumptions, embedded fonts, image resolution, export standard, page boxes, output intent/ICC profile, and final visual proofing.
- Home-print PDF: low ink, grayscale-safe, no essential full-bleed dependency, and clear cut/fold instructions.

The default print recommendation is PDF/X-4 for modern print workflows unless the printer explicitly asks for something else; final requirements always come from the printer/platform.

## What “no AI tells” means here

This package does not encourage deception. It means the finished artifact should read like careful human editorial and design work rather than generic model output. It removes meta-AI phrasing, vague filler, faux authority, hallucinated claims, copy-paste symmetry, generic fantasy voice, and unsupported production claims. It also requires evidence for citations, playtest statements, compatibility claims, and legal notices.

## Included scripts

- `check_contrast.py` - checks WCAG contrast for palette pairs in a YAML style guide.
- `manuscript_lint.py` - flags common AI tells, unsupported claims, TODOs, heading problems, missing front-matter signals, and TTRPG usability issues.
- `export_preflight.py` - triages PDF page sizes, page boxes, font diagnostics, image resolution hints, and external-tool diagnostics when available.
- `signature_planner.py` - calculates blank pages/signature breakdowns for booklets and sewn/saddle-stitched planning.

## Source citations

A curated citation file is included at `references/citations.md`. Individual standard files point back to sources such as W3C/WCAG, PDF Association, PDF/UA, DriveThruRPG, Lulu, Adobe, clear-print guidance, open RPG licensing references, and Python documentation.
