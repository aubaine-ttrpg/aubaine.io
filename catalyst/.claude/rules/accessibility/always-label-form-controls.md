---
name: always-label-form-controls
description: Every form control has a programmatic label, and its errors are associated and announced.
paths: ["templates/**", "src/Form/**", "assets/**", "tests/e2e/**"]
severity: must
---
# Always label form controls

**Rule:** Every input, select and textarea has a programmatic label, either `<label for>` or `aria-label` / `aria-labelledby`. A placeholder is not a label. Validation errors are tied to the field with `aria-describedby` and announced to assistive tech.

**Why:** Without a programmatic label a screen reader cannot name the field, and an unassociated error is never read out. Criteria: WCAG 1.3.1 Info and Relationships, 3.3.2 Labels or Instructions, 4.1.2 Name Role Value. Labels and error text are user-facing strings, so translate them, see i18n/never-hardcode-user-facing-strings. Roles and states are covered in [[always-use-correct-aria-roles]].

**Good / Bad:**
```twig
{# Bad: no label, placeholder only #}
<input type="email" placeholder="Email">

{# Good: associated label and error #}
<label for="email">{{ 'signup.email'|trans }}</label>
<input type="email" id="email" aria-describedby="email-error">
<p id="email-error" class="field-error">{{ 'signup.email.invalid'|trans }}</p>
```

**Enforced by:** axe in e2e plus review.
