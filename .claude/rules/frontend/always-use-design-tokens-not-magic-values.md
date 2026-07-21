---
name: always-use-design-tokens-not-magic-values
description: Style with the CSS custom-property design tokens catalyst consumes from sigil and BEM class names, never hardcoded hex colours or pixel magic values.
paths: ["catalyst/assets/**", "catalyst/templates/**"]
severity: must
---
# Always use design tokens, not magic values

**Rule:** Style with the CSS custom-property design tokens catalyst consumes from `@aubaine/sigil` (colour, spacing, radius, type scale) and BEM class names. Never hardcode a hex colour or a pixel value inline in a rule, and never redefine a sigil token inside catalyst.

**Why:** the tokens are the contract the light and dark themes swap, and they are owned by one shared design system so every consumer stays in step (docs/adr/0001-shared-design-system-sigil.md, custom CSS, no Tailwind). A loose `#8a5cf6` cannot be themed or audited; a token can. Contrast lives in the colour tokens, so tokens are also how we hold WCAG AA.

**Good / Bad:**
```css
/* Bad: unthemeable magic values. */
.btn { color: #8a5cf6; padding: 13px; border-radius: 4px; }

/* Good: sigil tokens, themeable and shared. */
.btn { color: var(--color-accent); padding: var(--space-3); border-radius: var(--radius-lg); }
```

**See also:** accessibility/always-meet-wcag-aa-and-rgaa, [[always-support-light-and-dark-mode]].

**Enforced by:** review.
