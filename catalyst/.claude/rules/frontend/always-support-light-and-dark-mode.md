---
name: always-support-light-and-dark-mode
description: Every view must work in both light and dark mode through semantic design tokens; never hardcode a colour that only works in one theme.
paths: ["templates/**", "assets/**"]
severity: should
---
# Always support light and dark mode

**Rule:** Build every view to render correctly in **both** light and dark themes. Style only through
**semantic design tokens** (`--color-surface`, `--color-text`, `--color-border`), never raw hex values,
so a theme swap is a token swap. Honour the OS preference (`prefers-color-scheme`) by default and respect
the user's explicit toggle (persisted). New components ship theme-aware from the start; a feature is not
done if it only looks right in one mode.

**Why:** Dark/light is an advertised, transverse product requirement, so it is part of
"done", not a later pass. Driving colour through tokens is what makes two themes (and the future shared
design-system package, ADR 0008) a swap rather than a rewrite, and it keeps both themes meeting the
WCAG AA contrast bar (accessibility/always-meet-wcag-aa-and-rgaa). Builds on
[[always-use-design-tokens-not-magic-values]].

**Good / Bad:**
```css
/* Bad: hardcoded, invisible in dark mode. */
.card { background: #ffffff; color: #111111; }

/* Good: semantic tokens defined per theme; the component never names a colour. */
.card { background: var(--color-surface); color: var(--color-text); }
:root { --color-surface: #fff; --color-text: #111; }
:root[data-theme="dark"], @media (prefers-color-scheme: dark) { --color-surface: #14161a; --color-text: #f3f4f6; }
```

**See also:** [[always-use-design-tokens-not-magic-values]], accessibility/always-meet-wcag-aa-and-rgaa.

**Enforced by:** review, axe (contrast in both themes).
