---
name: theme-via-tokens
description: Switch themes by re-pointing the public tokens through the three-pronged root pattern, keep component CSS theme-unaware, and clear WCAG AA in both themes.
paths: ["sigil/src/tokens/semantic.css", "sigil/src/**/*.css"]
severity: must
---
# A theme is a token swap

**Rule:** A theme differs only by which value-set the public aliases point at. Wire the swap with the three-pronged root pattern and nothing else:

- `:root` sets the light aliases and declares `color-scheme: light dark`.
- `@media (prefers-color-scheme: dark) :root:not([data-theme='light'])` re-points the aliases at the dark set, so the OS preference wins unless the user forced light.
- `:root[data-theme='dark']` re-points the same aliases, so an explicit choice wins over the OS.

Component CSS is theme-unaware: it reads `var(--color-*)` and never branches on `prefers-color-scheme` or `data-theme` for colour. Every foreground/background token pair clears WCAG AA in both themes: 4.5:1 for body text, 3:1 for large text and non-text UI. A view is not done until it holds that bar in light and dark.

**Why:** re-pointing aliases at one place (semantic.css) is what makes a theme a swap rather than a rewrite, and keeps light and dark from drifting (docs/adr/0001-shared-design-system-sigil.md; builds on [[tokens-single-source-of-truth]]). The three prongs together honour the OS default and still let the user override it (MDN `color-scheme`; frontend/always-support-light-and-dark-mode). Contrast lives in the token values, so both themes carry AA by construction (WCAG 1.4.3 Contrast Minimum, 1.4.11 Non-text Contrast; accessibility/always-meet-wcag-aa-and-rgaa). A component that inspects the theme itself defeats the swap and hides one path from contrast review.

**Good / Bad:**
```css
/* Bad: the component branches on the theme and hardcodes both colours. Untestable
   for contrast, and it duplicates what the token swap already owns. */
.panel { background: #fff; color: #211c17; }
@media (prefers-color-scheme: dark) { .panel { background: #16110c; color: #f2e9dc; } }

/* Good: the component is theme-unaware; semantic.css owns the swap. */
.panel { background: var(--color-surface); color: var(--color-text); }
```
```css
/* semantic.css: the three prongs re-point the same aliases. */
:root { --color-surface: var(--light-surface); color-scheme: light dark; }
@media (prefers-color-scheme: dark) {
    :root:not([data-theme='light']) { --color-surface: var(--dark-surface); }
}
:root[data-theme='dark'] { --color-surface: var(--dark-surface); }
```

**See also:** [[tokens-single-source-of-truth]], frontend/always-support-light-and-dark-mode, accessibility/always-meet-wcag-aa-and-rgaa.

**Enforced by:** axe (Storybook a11y addon, run in both themes) plus review.
