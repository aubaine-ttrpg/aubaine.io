---
name: content-accessibility
description: Published game text is structured so it reads cleanly by eye, in a screen reader, and in grayscale print.
paths: ["codex/**/*.md", "almanach/src/**"]
severity: should
---
# Content accessibility

**Rule:** Structure the published game text so it stays navigable and unambiguous for every reader.
- One H1 per document; headings nest by one level at a time, never skipping a level to fake a size.
- Define a term where it first appears, or link it to its glossary entry; do not restate the definition inline.
- Never carry a rule's meaning by colour alone. Pair any colour with a label, an icon, or a word.
- Keep the source order equal to the reading order, so a screen reader and a grayscale print follow the same path the eye does.
- Put every rule in the procedure text. A rule hidden in a flavour aside or a caption is a rule the reader misses.

**Why:** codex prose is the mechanics source of truth and almanach renders it for the public, so a heading that skips a level, a rule buried in flavour, or a colour-only cue breaks the reader who navigates by structure, by screen reader, or on a grayscale page. The readability standards this follows live in the aubaine-visual-supports-designs skill (`standards/accessibility_readability.md`); read them there rather than copying the checklist here.

**Good / Bad:**
```text
Bad:
# Combat
### Taking damage        (jumped from H1 to H3)
When you are hit, subtract... (Red text means it is a critical.)   (colour-only, rule in an aside)

Good:
# Combat
## Taking damage
When you are hit, subtract the damage from your health. A critical hit
(labelled "critical", with the burst icon) doubles it. See the glossary for "critical".
```

**See also:** [[always-meet-wcag-aa-and-rgaa]].

**Enforced by:** review; the aubaine-visual-supports-designs skill.
