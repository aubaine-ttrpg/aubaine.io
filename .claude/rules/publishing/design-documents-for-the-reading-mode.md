---
name: design-documents-for-the-reading-mode
description: A document serves the reading mode it is built for: rules run as procedures, tables drive decisions, and every page shows what it is, what to do, and where to go next.
paths: ["codex/**/*.md", "almanach/src/**", "catalyst/src/Pdf/**"]
severity: should
---
# Design documents for the reading mode

**Rule:** A book is a play tool, not a container for lore. Before you style a page, name the reading mode it serves (learning the game in order, looking a rule up at the table under pressure, reading it in print), then build for that mode. The set of modes lives in the aubaine-visual-supports-designs skill (`standards/bookcraft_layout.md`); pick one and commit to it. Then:

- Rules read as procedures. A reader runs them top to bottom without inferring a step (codex prose follows game-design/write-rules-as-procedures).
- Tables are decision tools, not decoration. The title states the table's job, the die range leads, and every result is something the group can act on.
- Every page answers three questions on sight: what am I looking at, what do I do, where do I go next. Signal that structure with consistent hierarchy, not a new style per sidebar.

A page that hides its structure, buries a mandatory procedure in a sidebar, or ships a table the GM cannot act on has not done its job.

**Why:** The aubaine-visual-supports-designs skill treats the book as a live-use interface and orders each page by what the table needs (`standards/bookcraft_layout.md`), and it requires rules to be active procedures and random tables to create decisions rather than flavor (`standards/ttrpg_craft.md`). A reader in a hurry at the table cannot parse a wall of prose or guess a missing step; the layout has to carry the answer. This is why codex mechanics keep a procedural shape and why the same discipline reaches almanach pages and catalyst's generated PDFs: the reading mode, not the medium, decides the layout.

**Good / Bad:**
```markdown
Bad: a decorative title and a result the GM cannot act on.
### Atmosphere
| Mood | Feeling |
| ---- | ------- |
| Dark | Scary   |

Good: the title states the table's job, the die range leads, each result acts.
### d6 What the search of the room turns up
| d6 | What the party finds |
| -- | -------------------- |
| 1  | A boot print, still wet; someone left within the hour. |
| 2  | A torn ledger page naming the next drop point. |
```

**See also:** [[keep-pdf-and-export-clean]], [[claim-nothing-you-have-not-verified]], game-design/write-rules-as-procedures.

**Enforced by:** the aubaine-visual-supports-designs skill (bookcraft and TTRPG craft standards) + review.
