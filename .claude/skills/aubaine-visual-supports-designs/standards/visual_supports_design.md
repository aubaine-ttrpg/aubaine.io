# Visual support design standards for tabletop RPGs

A TTRPG visual support is a designed object that helps play: book pages, PDF spreads, rules references, character sheets, GM-screen panels, cards, forms, trackers, handouts, indexes, glossaries, tables, and diagrams. Art can enrich these objects, but the design must work even when art is absent.

## Core question

Every page, panel, card, or sheet must answer:

1. **What am I looking at?** The object’s role is clear from title, position, hierarchy, and context.
2. **What do I do with it?** Procedures, choices, triggers, fields, and outcomes are visible.
3. **Where do I look next?** Navigation, cross-references, folios, labels, tabs, bookmarks, or section markers guide the user.
4. **Can I use this under pressure?** The design survives table noise, low light, quick lookup, handwriting, printing, and screen zoom.

## Design without relying on illustration

Prioritize structural visual language:

- Typography: type family, size, weight, width, leading, measure, tabular figures, real small caps.
- Grid: columns, margins, gutters, baseline, modular units, panel divisions, card safe zones.
- Hierarchy: headings, running heads, labels, folios, callouts, rules, borders, indents, spacing.
- Components: procedure boxes, examples, tables, stat blocks, NPC blocks, faction blocks, clocks, trackers, forms.
- Navigation: table of contents, bookmarks, page labels, cross-references, index, glossary, tabs, color bands plus labels.
- Redundant cues: color + icon + label + position + shape, never color alone.

Do not let decorative texture, borders, parchment effects, faux aging, or background art reduce legibility. If the art direction conflicts with usability, create an alternate accessible/clean edition.

## Reading modes

Design for at least four modes:

### Learning mode

- Progressive disclosure: introduce terms before using them heavily.
- Put the core loop early.
- Include examples immediately after dense rules.
- Use “first session” or “start here” paths for long books.

### Prep mode

- Give the GM procedures for preparing scenes, locations, factions, clocks, NPCs, clues, and consequences.
- Keep checklists and generators close to the chapters they support.
- Make locations and factions navigable through stable headings and page references.

### Table mode

- Use large enough type, short paragraphs, visible table labels, repeated headers, page references, and stable component order.
- Keep high-frequency rules out of long prose.
- Put one-screen/one-page summaries in predictable locations.

### Digital lookup mode

- Use bookmarks, links, page labels, selectable text, metadata, and semantic headings.
- Avoid baking labels, tables, rules, or stat blocks into images.
- Keep search terms consistent: do not alternate between several names for the same condition or procedure unless the glossary connects them.

## Component standards

### Procedure box

A procedure box should contain:

- Trigger: when to use it.
- Actor: who acts.
- Steps: numbered or strongly sequenced.
- Choice points: what decisions matter.
- Inputs: dice, stats, resources, tags, position, time.
- Outputs: fictional change, mechanical change, cost, risk, next procedure.
- Example or page reference when ambiguity is likely.

### Random table

A table is a generator, not decoration:

- Put die range or key in the first column.
- Title the table by use: `d6 Signs the Cult Was Here`, not `Atmosphere`.
- Make results actionable and distinct.
- Use repeated headers for multi-page tables.
- Keep row height adequate for annotation/home print when intended.
- Add page references where a result invokes a rule, NPC, map, or location.

### Stat block / NPC block

- Stable field order across the whole product.
- Put combat/time-critical fields first if the product is combat-heavy; motive/leverage first if social/investigation-heavy.
- Avoid tiny type, dense inline punctuation, and unlabelled abbreviations.
- Include what the NPC wants, what they do now, and how they react to pressure.

### Character or faction sheet

- Make fields pencil-friendly.
- Put labels outside write-in areas when possible.
- Preserve enough line height and whitespace for handwriting.
- Use visible grouping for creation, session use, advancement, and notes.
- Ensure grayscale and low-ink versions work.

### GM screen panel

- A screen is a dashboard, not a compressed book.
- Use type larger than body text.
- One panel should usually hold one functional cluster.
- Keep fold/panel seams clear of critical details.
- Prioritize high-frequency lookup over completeness.

### Handout

- Decide whether the handout is player-facing fiction, an in-world prop, a clue, a puzzle, or a rules aid.
- Keep player-facing facts legible even if the prop style is distressed.
- Add GM-only notes outside the player artifact or in a separate layer/file.

### Card/reference card

- One concept per card.
- Stable title and icon positions.
- High contrast and large enough type.
- Bleed/corner-radius safe zone if physically cut.
- No critical rules near rounded corners or trim.

## Visual hierarchy rules

- One primary focal point per page/panel/card.
- One typographic scale for the product; do not invent new heading sizes per chapter.
- Use spacing before relying on lines and boxes.
- Use boxes sparingly: if everything is boxed, nothing is highlighted.
- Give examples a consistent visual treatment.
- Keep page furniture useful: folios, section names, chapter titles, and running heads should aid navigation.
- Never hide mandatory rules in decorative marginalia.

## Digital-specific visual design

- Check page spreads as single pages and two-page spreads.
- Avoid tiny marginalia that only works on print spreads if the primary edition is digital.
- Include internal links from contents, index, glossary, rule references, and page-callouts.
- Use actual bookmarks, not only a visual table of contents.
- Keep file size reasonable without destroying text clarity or image quality.

## Print-specific visual design

- Treat bleed, trim, safety, gutter, fold, punch, and panel seams as part of the layout grid.
- Keep live text out of trim and binding hazard zones.
- Do not assume a beautiful screen proof will survive paper, ink, and lighting.
- Provide a low-ink or home-print edition if the table is expected to print sheets frequently.

## No-AI-tell design checks

Visual AI tells are not only prose problems. Check for:

- Over-symmetrical layouts that ignore content importance.
- Generic “fantasy parchment” treatment without functional reason.
- Inconsistent fake labels or decorative headings.
- Vague callouts with no procedure.
- Tables where every result has the same sentence rhythm.
- Icons used as decoration but not as navigation.
- Fake production confidence: “print-safe,” “accessible,” “balanced,” or “professionally laid out” without checks.

## Done means usable

A visual support is done only when someone can use it for its intended job without designer explanation. Test with a realistic task: create a character, run a scene, find a condition, print a sheet, follow a clue, use a handout, or check a rule mid-session.
