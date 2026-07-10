# Accessibility and readability standards for TTRPG visual supports

Accessible layout is not an afterthought. TTRPG books are reference tools used under time pressure, by groups, in low light, and often by readers with different visual, cognitive, and language needs.

## Contrast

- WCAG 2.2 contrast minimum for normal text is 4.5:1; large text is 3:1. Use this as a floor, not a ceiling.
- Non-text cues that communicate information should also meet contrast expectations; do not rely on color alone.
- For body text in print, aim for stronger contrast than screen minimums because paper, lighting, texture, and ink spread reduce perceived contrast.

Sources: W3C WCAG 2.2 and WAI understanding docs, citations 12-15.

## Type size and spacing

Default recommendations:

- Standard print body: 10.5-12 pt depending on typeface x-height and column measure.
- Accessible edition body: 12-14 pt, with larger line spacing.
- Leading: 1.25-1.5x body size; 1.5x is often better for dyslexia-friendly versions.
- Avoid long paragraphs in rules sections. Break into procedure, examples, choices, and outcomes.
- Avoid all caps and italics for extended text.
- Use left alignment by default. Fully justified text requires careful hyphenation and spacing checks.

Sources: British Dyslexia Association, RNIB, and clear-print guidance, citations 21-23.

## TTRPG-specific accessibility

- Provide a table-of-contents and quickstart path.
- Include bookmarks in digital PDF editions.
- Use semantic heading levels in source files.
- Use repeated headers for long tables.
- Use glossary and index terms consistently.
- Keep stat block fields in stable order across the book.
- Use redundant encodings: color + icon + label + position.
- Provide plain-text or simplified reference versions for highly art-directed books.
- For character sheets, ensure fields are large enough to write in by hand.
- For GM screens, make each panel self-contained: no cross-panel rule dependencies for common lookups.

Wired’s TTRPG accessibility coverage highlights that rulebooks must balance beauty with navigability and that accessible alternate editions can give players both artful and readable formats. See citation 24.

## Accessible PDFs

When producing a digital PDF, do not confuse a visually readable PDF with an accessible PDF. Check:

- Tagged PDF structure.
- Correct reading order.
- Document language.
- Title metadata.
- Bookmarks.
- Alt text for meaningful images.
- Table headers and scope.
- Selectable text.
- Logical tab order for forms.

PDF/UA is ISO 14289 and relies on Tagged PDF to represent semantic information; see citations 16-18. Adobe guidance recommends checking and repairing reading order, tags, alternate text, form labels, and tab order in Acrobat; see citations 19-20.

## Accessibility acceptance test

Before final delivery, answer yes to all:

- Can a first-time player find character creation, core mechanic, safety expectations, and first scene quickly?
- Can a GM find conditions, damage, NPC stats, encounter procedure, and random tables during play?
- Can a color-blind reader use the piece without interpreting color alone?
- Can pages be printed in grayscale and remain understandable?
- Can tables be read left-to-right without guessing relationships?
- Can the digital PDF be navigated with bookmarks/headings?
- Is there an accessible or printer-friendly edition if the art layout sacrifices readability?
