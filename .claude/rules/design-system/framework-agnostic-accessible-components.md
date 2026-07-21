---
name: framework-agnostic-accessible-components
description: Build sigil components as class contracts consumable by Twig and Astro, shipping no per-component JS, with native accessible markup and one documented modifier vocabulary per file.
paths: ["sigil/src/components/**"]
severity: should
---
# Framework-agnostic, accessible components

**Rule:** A component is a CSS class contract, nothing more. It is consumable identically by Catalyst's Twig and Almanach's Astro, so it ships no per-component JavaScript and assumes no framework runtime. Write examples on native accessible markup: a real `<button>` for an action, a real `<label>` tied to its control, and an `aria-label` on any icon-only button. Document one modifier vocabulary per component in a comment at the top of its file (the modifiers it offers and what each does); a consumer reads that comment to know the whole API.

**Why:** two surfaces render the same components, so the shared layer must be markup and class names both can emit; behaviour belongs to each consumer (Catalyst's Stimulus, Astro's islands), not to the design system (docs/adr/0001-shared-design-system-sigil.md). Native elements carry their role, focus, and keyboard behaviour for free, and an icon-only control with no text needs an accessible name (WCAG 4.1.2 Name Role Value; accessibility/always-label-form-controls, accessibility/always-use-correct-aria-roles). One modifier comment per file is the component's contract in one place, so the vocabulary does not drift from the CSS.

**Good / Bad:**
```css
/* Bad: no contract comment, and the modifiers live only in scattered rules. */
.btn--primary { background: var(--color-accent); }
.btn--gold { background: var(--color-gold); }
```
```css
/* Good: one modifier vocabulary, documented at the top of button.css. */
/* Buttons. Modifiers: --primary (void accent), --gold, --ghost, --danger. */
.btn--primary { background: var(--color-accent); color: var(--color-accent-ink); }
```
```html
<!-- Bad: a div acting as a button, and an icon with no accessible name. -->
<div class="btn btn--primary" onclick="save()">Save</div>
<button class="icon-btn">✕</button>

<!-- Good: native button, and the icon-only control names itself. -->
<button type="button" class="btn btn--primary">Save</button>
<button type="button" class="icon-btn icon-btn--danger" aria-label="Delete">✕</button>
```

**See also:** [[storybook-discipline]], [[css-architecture-bem-and-tokens]], accessibility/always-label-form-controls, accessibility/always-use-correct-aria-roles.

**Enforced by:** axe (Storybook a11y addon) plus review.
