---
name: always-meet-wcag-aa-and-rgaa
description: Every view must meet WCAG 2.1 level AA and RGAA, with an axe assertion in its e2e test.
paths: ["templates/**", "assets/**", "tests/e2e/**"]
severity: must
---
# Always meet WCAG 2.1 AA and RGAA

**Rule:** Every view meets WCAG 2.1 level AA and RGAA. Text contrast is at least 4.5:1 (3:1 for large text), the layout survives text resized to 200%, status is never signalled by colour alone, and each view's e2e test runs an axe assertion. This is the umbrella rule, the specifics live in [[always-label-form-controls]], [[always-keep-focus-visible-and-keyboard-operable]] and [[always-use-correct-aria-roles]].

**Why:** Accessibility is mandatory here, not optional (ADR 0011). RGAA and WCAG 2.1 AA are legal and contractual baselines for a French B2B SaaS. Relevant criteria: WCAG 1.4.3 Contrast, 1.4.4 Resize Text, 1.4.1 Use of Color. Pull contrast values from the design tokens, never hardcode hex, see frontend/always-use-design-tokens-not-magic-values.

**Good / Bad:**
```twig
{# Bad: meaning carried only by colour #}
<span class="dot dot--red"></span>

{# Good: colour plus text and icon #}
<span class="status status--overdue">
  {{ ux_icon('alert') }} {{ 'invoice.status.overdue'|trans }}
</span>
```

**Enforced by:** axe in e2e (every view) plus review.
