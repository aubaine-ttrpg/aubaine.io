---
name: css-architecture-bem-and-tokens
description: Write sigil CSS as flat single-class BEM styled only through tokens, with one global focus ring, !important reserved for motion escapes, and no hand-written vendor prefixes.
paths: ["sigil/src/**/*.css"]
severity: must
---
# CSS architecture: BEM and tokens

**Rule:**

- Name with BEM: `.block`, `.block__element`, `.block--modifier`. Style at a flat single-class specificity. No id selectors, and no long descendant chains used to win specificity.
- Style only through tokens (see [[tokens-single-source-of-truth]]); the raw-value exceptions defined there are the only ones.
- Define the focus ring once, globally, in the reset, from `--color-focus-ring` and `--ring-*`. Components do not restyle it. If a component must relocate the ring (a stretched card link), it re-applies the same tokens; it never leaves a bare `outline: none`.
- Use `!important` only inside a `prefers-reduced-motion: reduce` escape or the forced-transition helper that eases a theme swap. Nowhere else.
- Do not hand-write the prefixed clone of a standard property as your styling mechanism; write the standard property and let each consumer's autoprefixer add the prefixes its `.browserslistrc` calls for. Genuinely vendor-only properties and pseudo-elements that have no standard form (`-webkit-font-smoothing`, `-webkit-text-fill-color`, `::-webkit-details-marker`) are written directly; that is the small intentional set, not a licence to prefix by hand.

**Why:** flat single-class BEM keeps specificity predictable, so a consumer can compose on top without a specificity war, and id selectors and descendant chains are the usual cause of unoverridable rules. One token-driven focus ring means every interactive element is keyboard-visible with the same look, and a lone `outline: none` is the classic keyboard trap (WCAG 2.4.7 Focus Visible; accessibility/always-keep-focus-visible-and-keyboard-operable). Reserving `!important` for the motion escapes keeps it meaningful where a preference must override everything. Autoprefixer owning prefixes is the one place to widen or narrow browser support, and hand prefixes drift and double up (frontend/use-browserslist-and-autoprefixer).

**Good / Bad:**
```css
/* Bad: id + descendant chain for specificity, a re-invented focus colour, and a
   hand-written prefix pair for a property autoprefixer already covers. */
#sidebar ul li a.item:focus-visible { outline: 2px solid #9d6fd1; }
.item { -webkit-border-radius: 2px; border-radius: 2px; }

/* Good: flat BEM class, tokens, no per-component focus override, standard property. */
.sidenav__item { border-radius: var(--radius); color: var(--color-text); }
```

**See also:** [[tokens-single-source-of-truth]], [[respect-reduced-motion]], frontend/use-browserslist-and-autoprefixer, accessibility/always-keep-focus-visible-and-keyboard-operable.

**Enforced by:** review (autoprefixer runs in each consumer's Encore/Vite build).

## Design Notes

Non-normative. Going forward, prefer wrapping the layers in `@layer` (reset, base, components) so cascade order is explicit rather than import-order dependent, and prefer logical properties (`margin-inline`, `padding-block`, `inset`) over physical ones for future right-to-left support. Neither is mandated today; the current code predates both.
