---
name: aubaine-visual-supports-designs
author: Aymen Ezzayer
version: 1.1.0
last_updated: 2026-07-09
license: MIT
description: Create polished tabletop RPG visual supports and publication artifacts like rulebooks, modules, zines, PDFs, sheets, handouts, cards, GM screens, reference spreads, and readable play aids. Focuses on layout, information design, typography, accessibility, PDF/export hygiene, front matter, legal/credit hygiene, and no-AI-tell editorial polish. Not for standalone artwork or image generation.
---

# Aubaine Visual Supports Designs Skill

## Purpose

Use this skill to design the **visual supports around tabletop play**, not to generate art. The skill creates and critiques the designed objects that players and GMs read, hold, search, print, annotate, and use at the table:

- Rulebooks, campaign books, quickstarts, adventure modules, ashcans, zines, pamphlets, and booklets.
- Screen-readable PDFs, accessible PDFs, printer-friendly PDFs, home-print editions, and print-on-demand exports.
- Character sheets, faction sheets, trackers, worksheets, safety cards, reference cards, handouts, inserts, GM-screen panels, map-key pages, rules summaries, indexes, glossaries, and table-facing aids.
- Publication systems: grids, typographic hierarchy, page architecture, component templates, front matter, back matter, colophons, rights ledgers, export notes, and QA checklists.

Do **not** use this as a standalone artwork/image-generation skill. It may define art direction, art slots, captions, map-label treatment, icon systems, alt text, and production constraints for images, but the primary work is **layout, readability, visual information design, and publication artifact production**.

## Operating principles

1. **Support play first.** A TTRPG artifact is a live-use interface. Design for learning, prep, table lookup, reference, annotation, and post-session upkeep.
2. **Visual design without art dependency.** Use typography, grids, spacing, rules, icon labels, table architecture, page furniture, and structure before relying on illustration.
3. **No AI tells.** Remove meta-AI phrasing, generic fantasy filler, smooth-but-empty prose, copy-paste symmetry, false authority, fake citations, and unsupported claims. Preserve a specific authorial voice.
4. **Readable in multiple contexts.** Build separate or adaptable outputs for screen reading, print, home printing, accessibility, mobile/tablet lookup, and table-facing handouts.
5. **Print-ready when print is requested.** Treat print as an export mode with trim, bleed, safety, page boxes, output intent, embedded fonts, CMYK/ICC choices, proofing, and printer-specific requirements.
6. **Accessible, not sterile.** Use semantic headings, real text, bookmarks, logical reading order, strong contrast, usable forms, repeated table headers, redundant cues, and alternative accessible editions when highly art-directed layout reduces comprehension.
7. **Rules are procedures.** Present rules as triggers, choices, costs, fictional effects, outcomes, examples, and lookup paths rather than lore walls.
8. **Legal and credit hygiene.** Track licenses, compatibility claims, SRDs, ORC/OGL/CC notices, fonts, icons, maps, stock assets, and third-party text. Never fabricate permissions, playtest claims, endorsements, or printer approval.
9. **Verify visually and structurally.** Render PDFs, inspect pages, check contrast, lint manuscripts, test navigation, and record export assumptions before calling a deliverable finished.
10. **Clean source, clean export.** Keep reusable source files, style guides, templates, scripts, and production notes understandable enough for another editor/designer to maintain.

## Default assumptions when details are missing

Use these to keep work moving, then record them in `templates/project_brief.yml` and `templates/production_notes.md`:

- Artifact type: rulebook/module if prose-heavy; sheet/handout/reference card if table-facing; screen PDF if digital-first.
- Interior size: `6 x 9 in` for a book/module, `A5` for a zine/booklet, `US Letter/A4` for sheets, or explicit device-independent PDF dimensions for screen-first supports.
- Output modes: screen PDF and source package by default; printer PDF only when print/POD/home-print is requested or likely.
- Bleed for print: `0.125 in / 3.18 mm` unless the printer specifies otherwise; do not assume binding-edge bleed rules across vendors.
- Safety margin for print: at least `0.5 in / 12.7 mm` for live text; increase near binding, fold, punch, or panel seams.
- Body text: 10.5-12 pt for standard print, 12-14 pt for accessible/large-print editions; use adequate leading and line length.
- Contrast: WCAG AA as a floor for digital/screen preview; aim higher for body text, tables, cards, screens, and physical reading under poor light.
- Export: PDF/X-4 for modern professional print unless the printer requires a different standard or platform preset; tagged/bookmarked PDF for digital accessibility.
- Images/art, if present: 300 ppi at final print size for raster art; keep rules text, labels, numbers, and captions as real text whenever possible.

## Required workflow

### 1) Brief the artifact, not just the format

Create or update `templates/project_brief.yml`. Capture:

- Artifact type, audience, table-use context, reading mode, target devices/paper, intended lifetime, edition type, and accessibility requirements.
- Component inventory: interior PDF, screen PDF, printer PDF, accessible PDF, home-print PDF, cover, cards, sheets, handouts, GM-screen panels, inserts, errata sheet, plain text/reference edition.
- Game context: system, license basis, compatibility/attribution needs, GM/player split, table procedures, playtest evidence, and safety expectations.
- Production assumptions: trim/page size, binding/fold/panel model, bleed/safety, color model, print vendor if known, export standard, font licensing, asset ownership.

### 2) Build information architecture before styling

Design the user paths first:

- **Learning path:** What must a first-time reader understand in the first 10 minutes?
- **Table path:** What must the GM/player find mid-session in 5-15 seconds?
- **Reference path:** Where are repeated procedures, conditions, tables, examples, indexes, and terms?
- **Digital path:** How do bookmarks, links, page labels, search terms, and headings work?
- **Print path:** How do spreads, folios, gutter, bleed, fold, binding, and annotation space work?
- **Accessibility path:** What is the reading order, heading structure, alt text strategy, and simplified edition plan?

### 3) Editorial and no-AI-tell pass

Apply `standards/no_ai_tell_editorial.md` and `standards/ttrpg_craft.md`:

- Replace generic atmosphere with specific hooks, stakes, locations, constraints, costs, and consequences.
- Convert lore walls into play-facing procedures, rumors, faction moves, clocks, encounter tables, clues, and handouts.
- Make every table scannable: die ranges, actions, outcomes, escalation, evidence, and page references.
- Remove unsupported claims such as “fully accessible,” “print-ready,” “playtested extensively,” “balanced,” or “industry standard” unless evidence exists.
- Keep voice intentional: concise, tactile, specific, table-facing, and free of meta-AI language.

### 4) Visual support design pass

Apply `standards/visual_supports_design.md`, `standards/bookcraft_layout.md`, and `standards/accessibility_readability.md`:

- Establish grid, margins, typographic scale, heading hierarchy, table styles, sidebars, callouts, examples, stat blocks, icons, folios, running heads, and component templates.
- Use visual hierarchy to answer “what am I looking at, what do I do, where do I look next?”
- Use color, icons, shapes, borders, labels, and placement redundantly; never rely on color alone.
- Keep core procedures out of decorative sidebars unless the sidebar is part of the procedure.
- For sheets and forms, protect writing areas, labels, tab order, and printable grayscale usability.
- For PDFs, include bookmarks, hyperlinks, page labels, selectable text, metadata, and accessible structure where possible.

### 5) Front matter, back matter, and rights pass

Use `templates/front_matter.md`, `templates/colophon.md`, `templates/rights_ledger.yml`, and `standards/legal_and_licensing.md`:

- Title page, credits/copyright, license/attribution, compatibility disclaimer, accessibility note, safety note when appropriate, table of contents, “how to use this,” and quickstart/reading path.
- Back matter: index, glossary, rules reference, tables appendix, sheets/handouts, license notices, errata URL, asset credits, and production colophon.
- Record fonts, asset licenses, source links, generated/stock/manual assets, modifications, export date, and printer/display assumptions.

### 6) Output and PDF pass

Apply `standards/output_production_pdf.md` and run the relevant scripts:

```bash
python scripts/check_contrast.py templates/style_guide.yml
python scripts/manuscript_lint.py path/to/manuscript.md
python scripts/export_preflight.py path/to/export.pdf --expected-page-size 6.25x9.25in
python scripts/signature_planner.py 68 --signature-size 16
```

Use the output target intentionally:

- **Screen PDF:** small enough to share, searchable, bookmarked, linked, readable on tablets/laptops.
- **Accessible PDF:** tagged, logical reading order, document language, alt text, table headers, form labels, bookmarks.
- **Printer PDF:** printer preset or PDF/X target, bleed/safety, embedded fonts, output intent/ICC profile, correct page boxes.
- **Home-print PDF:** low ink, grayscale-safe, no essential full-bleed requirements, clear cut/fold instructions.
- **Source package:** source files, manifest, rights ledger, style guide, export settings, production notes.

### 7) Acceptance criteria

A deliverable is not ready until:

- Its artifact type, audience, use mode, and output targets are documented.
- Navigation works: headings, TOC, bookmarks, page labels, links, folios, cross-references, index/glossary where relevant.
- Tables, stat blocks, examples, sheets, cards, and handouts are scannable and consistent.
- Body text, table text, labels, and non-text cues meet contrast/readability goals.
- Digital PDFs preserve selectable text, metadata, bookmarks, and accessible structure where possible.
- Print/POD exports document trim, bleed, safety, page boxes, fonts, image resolution, output intent, color model, and printer assumptions.
- No obvious AI tells, lorem ipsum, hallucinated citations, missing credits, fake legal permissions, or unsupported production claims remain.
- Final PDFs have been rendered and inspected page-by-page for clipping, unreadable type, table breaks, orphaned headings, widows, overprint/color mistakes, page-box mistakes, and broken links.

## Package map

- `standards/` - visual support design, bookcraft/layout, accessibility, TTRPG craft, no-AI-tell editorial, legal/rights, output/PDF production, and clean code/PDF standards.
- `workflows/` - reusable workflows for rulebooks, adventures, zines/booklets, sheets, handouts, GM screens, digital PDFs, and final output preflight.
- `templates/` - YAML briefs, style guides, front matter, colophons, content outlines, rights ledgers, and production notes.
- `scripts/` - contrast checking, manuscript linting, PDF/export triage, and signature planning.
- `examples/mini_zine/` - minimal sample source files.
- `references/citations.md` - standards and source citations used by this skill.

## Citation discipline

When giving production guidance, cite `references/citations.md` or the relevant standard file. For active client work, also cite the printer/platform’s current file-preparation guide because POD and digital marketplace requirements change.

Do not claim PDF/X, PDF/UA, accessibility, legal, or printer compliance from this skill alone. The scripts provide triage. Formal compliance requires appropriate validators, manual review, and, for print, the printer/platform’s approval.
