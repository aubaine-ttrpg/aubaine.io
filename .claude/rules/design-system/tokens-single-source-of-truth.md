---
name: tokens-single-source-of-truth
description: Author every sigil colour once in a three-tier token chain so the two themes cannot drift, and consume colour only through tokens.
paths: ["sigil/src/tokens/**"]
severity: must
---
# Tokens are the single source of truth

**Rule:** Every colour flows through three tiers and is authored once:

- **Brand primitives** in `brand.css` (the gold ramp, the void/purple ramp, ink, frame hairlines): the fixed identity values that do not change with the theme.
- **Private value-sets** in `semantic.css` (`--light-*` and `--dark-*`): each theme's concrete values, each written exactly once.
- **Public tokens** (`--color-*`, `--shadow-*`, `--grain-*`): these only alias a value-set (`var(--light-surface)`), never a literal. A theme swap re-points the aliases at the other set, so the two themes stay defined side by side and cannot drift.

Name a token by its intent (`--color-surface`, `--color-danger`), never its appearance (`--color-beige`). Components reference colour only through `var(--color-*)` / `var(--gold*)` tokens or a `color-mix()` built from them. The one documented raw-value exception is the dark ink placed on a gold fill (`#2a1e05`), a fixed high-contrast pairing that is not a theme colour; a bare white or black used as a `color-mix()` lightening or darkening endpoint is a neutral operand, not a palette colour. Game-data colours (domains, characteristics, papers) stay owned by the PHP enums and are never copied into CSS.

**Why:** one authored value per fact is the no-drift discipline (ai/never-create-drift, process/own-canonical-sources-of-truth): a theme is a token swap, not a second palette to keep in sync, so light and dark cannot fall out of step (docs/adr/0001-shared-design-system-sigil.md). Intent names survive a repaint; appearance names lie the moment the value changes. Enum-owned game colours have one home, so they never diverge between the PDF, the enums, and the CSS.

**Good / Bad:**
```css
/* Bad: a public token holding a literal, and an appearance name. Now dark has
   nowhere to point, and "beige" is a lie as soon as the value shifts. */
:root {
    --color-beige: #f4eee2;
    --color-surface: #f4eee2;
}

/* Good: primitive -> private per-theme value-set -> public alias, named by intent. */
:root {
    --light-surface: oklch(0.975 0.012 82);
    --dark-surface: oklch(0.16 0.01 45);
    --color-surface: var(--light-surface);   /* alias only */
}
```

**See also:** [[theme-via-tokens]], [[the-pdf-is-the-token-source-of-truth]], [[css-architecture-bem-and-tokens]], frontend/always-use-design-tokens-not-magic-values.

**Enforced by:** review.
