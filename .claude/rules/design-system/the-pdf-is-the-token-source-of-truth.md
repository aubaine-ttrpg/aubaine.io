---
name: the-pdf-is-the-token-source-of-truth
description: Treat the brand colour and font-role token values as the printed-book authority, keep print.css importing only those two token files, and check a rendered page before changing a value.
paths: ["sigil/src/tokens/brand.css", "sigil/src/tokens/fonts.css"]
severity: must
---
# The PDF is the token source of truth

**Rule:** The values in `brand.css` (gold ramp, ink, frame hairlines) and `fonts.css` (the serif and UI font roles) are the printed book's authority, not a copy of it. Catalyst's `print.css` drops its own `:root` and imports these two token files, and only these two: it never imports `web.css`, `semantic.css`, the base layer, or any component CSS. The printed pages compute the same values they always have. Changing any value in these two files changes the PDF, so a change is verified by a rendered-page check, not a CSS-text diff.

**Why:** the printed books are the oldest and most finished surface, so their gold and fonts are the real brand, and the shared system takes them as the source of truth without moving the pages a pixel (docs/adr/0001-shared-design-system-sigil.md). Print pulling only tokens is what keeps the PDF free of web-only surface, base, and component rules. The content-addressed PDF cache re-keys when the print bundle's bytes change, which is expected and self-invalidating, so text-identical is not the guard; a rendered page is.

**Good / Bad:**
```css
/* Bad: print.css pulls the whole web entry, dragging reset, components, and the
   dark theme into the PDF, and it re-declares gold, so two sources can disagree. */
@import '@aubaine/sigil/web.css';
:root { --gold: #efbe04; }

/* Good: print.css imports only the two token files; the values are the PDF's own. */
@import '@aubaine/sigil/tokens/brand.css';
@import '@aubaine/sigil/tokens/fonts.css';
```

**See also:** [[tokens-single-source-of-truth]], [[cross-consumer-consistency]], [[stable-public-api-and-versioning]].

**Enforced by:** review (rendered-page check on any value change).
