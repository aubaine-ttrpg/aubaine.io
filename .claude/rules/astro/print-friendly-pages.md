---
name: print-friendly-pages
description: Ship an @media print stylesheet that prints main only with readable ink, keeps tables and callouts whole, hides the chrome, and needs no JavaScript.
paths: ["almanach/src/styles/**", "almanach/src/layouts/**"]
severity: should
---
# Print-friendly pages

**Rule:**
- Ship an `@media print` stylesheet that makes any page printable on its own.
- Set readable ink: full-contrast text on a white page, driven by sigil's ink token, not a hardcoded brand colour.
- Add `break-inside: avoid` to tables and callouts so they do not split across pages.
- Hide the chrome: navigation, the rail, the intro overlay, the theme switch. Print `<main>` only.
- Mark PDF and other download links with `data-no-swup`.
- Printing must not depend on JavaScript.

**Why:** The game is meant to be read online, downloaded, and printed, so a printable page is part of the deliverable (README.md). `break-inside: avoid` keeps a table or a callout whole so it stays legible. Full-contrast ink through the sigil token keeps colour owned by the design system ([[consume-sigil-and-swup-transitions]]) and holds the readability bar the screen themes carry (accessibility/always-meet-wcag-aa-and-rgaa), while dropping chrome saves toner. `data-no-swup` lets a PDF link full-load instead of being swapped by Swup (docs/adr/0002-page-transitions-and-play-once-intro.md). With JavaScript off every page still loads (docs/adr/0002-page-transitions-and-play-once-intro.md), so print, the most script-hostile context, must never need it ([[static-first-and-island-discipline]]).

**Good / Bad:**
```css
/* Bad: no print rules, so the dark surface, the nav, and the rail all print, and tables split. */
@media print { }
```
```css
/* Good: print main only, readable ink, blocks kept whole. */
@media print {
  :root { color-scheme: light; }
  nav, .rail, .sidebar, .theme-switch, .intro { display: none !important; }
  main { color: var(--color-ink); background: #fff; }
  table, figure, .callout { break-inside: avoid; }
}
```
```astro
<!-- Good: the PDF link opts out of Swup so it full-loads (ADR 0002). -->
<a href={pdfUrl} data-no-swup download>Download the PDF</a>
```

**See also:** [[static-first-and-island-discipline]], [[consume-sigil-and-swup-transitions]], accessibility/always-meet-wcag-aa-and-rgaa.

**Enforced by:** review (print preview in both themes, with JavaScript disabled).
