---
name: always-label-form-controls
description: Every form control has a programmatic label, and its errors are associated and announced.
paths: ["catalyst/templates/**", "catalyst/src/Form/**", "catalyst/assets/**", "almanach/src/**/*.astro"]
severity: must
---
# Always label form controls

**Rule:** Every input, select, and textarea has a programmatic label, either `<label for>` or `aria-label` / `aria-labelledby`. A placeholder is not a label. Validation errors are tied to the field with `aria-describedby` and announced to assistive tech.

**Why:** Without a programmatic label a screen reader cannot name the field, and an unassociated error is never read out. Criteria: WCAG 2.2 1.3.1 Info and Relationships, 3.3.2 Labels or Instructions, 4.1.2 Name Role Value. Labels and error text are user-facing strings, so translate them (see i18n/never-hardcode-user-facing-strings). Roles and states are covered in [[always-use-correct-aria-roles]].

**Good / Bad:**
```twig
{# Bad: no label, placeholder only #}
<input type="text" placeholder="Title">

{# Good: associated label and error #}
<label for="book_title">{{ 'book.title'|trans }}</label>
<input type="text" id="book_title" aria-describedby="book_title-error">
<p id="book_title-error" class="field-error">{{ 'book.title.required'|trans }}</p>
```

**See also:** [[always-use-correct-aria-roles]], [[always-meet-wcag-aa-and-rgaa]], i18n/never-hardcode-user-facing-strings.

**Enforced by:** axe (Playwright end-to-end) plus review.
