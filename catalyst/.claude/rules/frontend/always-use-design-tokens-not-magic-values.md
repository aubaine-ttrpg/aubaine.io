---
name: always-use-design-tokens-not-magic-values
description: Style with CSS custom-property design tokens and BEM, never hardcoded hex colors or pixel magic values.
paths: ["assets/**", "templates/**"]
severity: must
---
# Always use design tokens, not magic values

**Rule:** Style with CSS custom-property design tokens (color, spacing, radius, type scale) and BEM class names. Never hardcode a hex color or a pixel value inline in a rule.

**Why:** Tokens are the contract the dark and light themes swap, and the contract the future shared design-system npm package will export (ADR 0008, "custom CSS, no Tailwind"). A loose `#1a73e8` cannot be themed or audited; a token can. Contrast lives in the color tokens, so tokens are also how we keep WCAG AA.

**Good / Bad:**
```css
/* Bad - unthemeable magic values */
.btn { color: #1a73e8; padding: 13px; border-radius: 4px; }

/* Good - tokens, themeable and shareable */
.btn { color: var(--color-primary); padding: var(--space-3); border-radius: var(--radius-sm); }
```

**See also:** accessibility/always-meet-wcag-aa-and-rgaa.

**Enforced by:** review.
