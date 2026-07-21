---
name: always-meet-wcag-aa-and-rgaa
description: Every catalyst and almanach view meets WCAG 2.2 level AA, checked by an axe assertion in its end-to-end test.
paths: ["catalyst/templates/**", "catalyst/assets/**", "almanach/src/**/*.astro"]
severity: must
---
# Always meet WCAG 2.2 AA

**Rule:** Every view meets WCAG 2.2 level AA, in both the catalyst authoring tool and the public almanach site. Text contrast is at least 4.5:1 (3:1 for large text), the layout survives text resized to 200%, status is never signalled by colour alone, and each view's end-to-end test runs an axe assertion. This is the umbrella rule; the specifics live in [[always-label-form-controls]], [[always-keep-focus-visible-and-keyboard-operable]], and [[always-use-correct-aria-roles]]. Published game text carries its own structure rules in [[content-accessibility]].

**Why:** Accessibility is part of "done" for every view, not a later pass, because both a solo author and public readers depend on it. Relevant criteria: WCAG 2.2 1.4.3 Contrast, 1.4.4 Resize Text, 1.4.1 Use of Colour. Pull contrast values from the sigil design tokens, never hardcode hex (see frontend/always-use-design-tokens-not-magic-values), so both themes clear the AA bar.

**Good / Bad:**
```twig
{# Bad: meaning carried only by colour #}
<span class="dot dot--red"></span>

{# Good: colour plus text and icon #}
<span class="status status--draft">
  {{ ux_icon('circle-dot') }} {{ 'book.status.draft'|trans }}
</span>
```

**See also:** [[always-label-form-controls]], [[always-keep-focus-visible-and-keyboard-operable]], [[always-use-correct-aria-roles]], [[content-accessibility]], frontend/always-use-design-tokens-not-magic-values, frontend/always-support-light-and-dark-mode.

**Enforced by:** axe (Playwright end-to-end, every view) plus review.
