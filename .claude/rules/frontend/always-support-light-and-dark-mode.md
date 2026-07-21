---
name: always-support-light-and-dark-mode
description: Every view works in both light and dark mode through semantic sigil design tokens; never hardcode a colour that only works in one theme.
paths: ["catalyst/templates/**", "catalyst/assets/**"]
severity: should
---
# Always support light and dark mode

**Rule:** Build every view to render correctly in **both** light and dark themes. Style only through the
**semantic design tokens** catalyst consumes from `@aubaine/sigil` (`--color-surface`, `--color-text`,
`--color-border`), never raw hex values, so a theme swap is a token swap. Catalyst consumes those tokens;
it does not redefine them. Honour the OS preference (`prefers-color-scheme`) by default and respect the
user's explicit toggle, persisted to `data-theme` on `<html>`. A feature is not done if it only looks
right in one mode.

**Why:** light and dark are both first-class, so theme support is part of "done", not a later pass.
Driving colour through the shared tokens is what makes two themes a swap rather than a rewrite, and it is
why catalyst reads from one design system instead of owning its own palette
(docs/adr/0001-shared-design-system-sigil.md). Both themes carry their contrast in the tokens, which keeps
them at the WCAG AA bar (accessibility/always-meet-wcag-aa-and-rgaa). Builds on
[[always-use-design-tokens-not-magic-values]].

**Good / Bad:**
```css
/* Bad: hardcoded, invisible in dark mode. */
.book-card { background: #ffffff; color: #111111; }

/* Good: consume the sigil semantic tokens; the component never names a colour, and sigil owns the
   per-theme values behind :root[data-theme="dark"] / prefers-color-scheme. */
.book-card { background: var(--color-surface); color: var(--color-text); }
```

**See also:** [[always-use-design-tokens-not-magic-values]], accessibility/always-meet-wcag-aa-and-rgaa.

**Enforced by:** review, axe (contrast in both themes).
