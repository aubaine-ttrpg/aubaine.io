---
name: always-use-correct-aria-roles
description: Prefer native HTML elements, add ARIA only to fill gaps with correct roles and states.
paths: ["catalyst/templates/**", "catalyst/assets/**", "almanach/src/**/*.astro"]
severity: must
---
# Always use correct ARIA roles

**Rule:** Reach for native HTML first (`<button>`, `<nav>`, `<table>`); it ships roles, states, and keyboard behaviour for free. Add ARIA only to fill a genuine gap, with correct roles and states (`aria-expanded`, `aria-controls`, `aria-live` for async updates). Do not add roles that duplicate a native element's own semantics.

**Why:** A wrong or redundant role tells assistive tech the wrong thing, and async DOM changes go unannounced without a live region. Standards: WAI-ARIA, WCAG 2.2 4.1.2 Name Role Value. Favour Twig and Live Components over hand-rolled widgets (see frontend/prefer-twig-and-live-components). Labelling is in [[always-label-form-controls]].

**Good / Bad:**
```twig
{# Bad: a div pretending to be a button #}
<div role="button" onclick="submit()">{{ 'action.save'|trans }}</div>

{# Good: native button, plus a live region for a Live Component re-render #}
<button type="submit">{{ 'action.save'|trans }}</button>
<div aria-live="polite" id="page-list">{# re-rendered by the Live Component #}</div>
```

**See also:** [[always-label-form-controls]], [[always-meet-wcag-aa-and-rgaa]], frontend/prefer-twig-and-live-components.

**Enforced by:** axe (Playwright end-to-end) plus review.
