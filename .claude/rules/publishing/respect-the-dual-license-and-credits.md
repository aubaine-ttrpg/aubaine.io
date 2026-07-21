---
name: respect-the-dual-license-and-credits
description: Code is MIT and all game content is CC BY-NC-SA 4.0; keep a rights ledger and correct the credits, copyright, and compatibility wording before layout.
severity: should
---
# Respect the dual license and credits

**Rule:**

- Two licenses, one repo. Code is MIT (`LICENSE`). All game content, prose, tables, art, and layouts, is CC BY-NC-SA 4.0 (`LICENSE-CONTENT`). Know which license each artifact falls under and label it correctly. Do not ship content under the code license or code under the content license.
- Keep a rights ledger before layout: every text, font, and asset paired with its source and terms. Track third-party or licensed material line by line. Assume nothing is cleared.
- Get credits, copyright, and compatibility wording right before layout, not after. Name the copyright holder and year, the license and its link, and whether changes were made. State compatibility without implying affiliation or endorsement.
- Never use protected names, trade dress, logos, or setting-specific creatures you hold no license for, and never fabricate endorsements, playtester quotes, or an "all rights cleared" line.

**Why:** The aubaine-visual-supports-designs skill gives the rights-ledger, credits-page, and compatibility-wording checklists and lists the prohibited shortcuts (`standards/legal_and_licensing.md`). CC BY-NC-SA 4.0 requires attribution, the license link, and a note if changes were made, and its NonCommercial and ShareAlike terms bind every downstream copy; mislabeling content as MIT strips protections the project depends on. A borrowed trade dress or an invented endorsement is a legal exposure the front matter creates permanently, so the wording is fixed before pages are laid out, not patched after.

**Good / Bad:**
```text
Bad: no license split, borrowed identity, invented endorsement.
(c) 2026 Aubaine. All rights reserved.
Official [big-brand RPG] expansion, endorsed by its makers. Every right cleared.

Good: correct dual-license notice and honest compatibility.
Code: MIT (see LICENSE). Text, tables, and art: CC BY-NC-SA 4.0 (see LICENSE-CONTENT).
(c) 2026 Aubaine. Compatible with [system] under [license]; independent, and not
affiliated with or endorsed by [rights holder].
```

**See also:** [[claim-nothing-you-have-not-verified]], [[design-documents-for-the-reading-mode]].

**Enforced by:** review + the aubaine-visual-supports-designs skill (`standards/legal_and_licensing.md`, rights ledger and credits page).
