---
name: never-put-business-logic-in-twig
description: Precompute values in PHP and pass ready-to-render data to Twig; templates present, they do not calculate, query, or filter.
paths: ["templates/**", "src/**/Controller/**", "src/**/View/**"]
severity: should
---
# Never put business logic in Twig

**Rule:** Compute totals, filters, and conditions in PHP (controller, service, or view DTO) and hand Twig data that is ready to render. No arithmetic in `{% set %}`, no querying, no filtering collections inside the template.

**Why:** Twig is for presentation, and it runs logic slower than PHP. Rules buried in a template cannot be unit-tested and get copied across views. A view DTO keeps the template readable and the math in one tested place. See architecture/always-thin-controllers.

**Good / Bad:**
```twig
{# Bad - the template sums and filters #}
{% set totalDue = 0 %}
{% for p in payments if p.status == 'unpaid' %}
  {% set totalDue = totalDue + p.amountCents %}
{% endfor %}
<p>{{ (totalDue / 100)|number_format(2) }} EUR</p>

{# Good - PHP did the work, Twig just prints #}
<p>{{ view.totalDueFormatted }} EUR</p>
```

**See also:** architecture/always-thin-controllers, architecture/always-use-dtos-at-boundaries.

**Enforced by:** review.
