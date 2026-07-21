---
name: never-put-business-logic-in-twig
description: Precompute values in PHP and pass ready-to-render data to Twig; templates present, they do not calculate, query, or filter.
paths: ["catalyst/templates/**", "catalyst/src/Controller/**", "catalyst/src/Twig/**"]
severity: should
---
# Never put business logic in Twig

**Rule:** Compute counts, filters, and conditions in PHP (controller, service, or view DTO) and hand Twig data that is ready to render. No arithmetic in `{% set %}`, no querying, no filtering collections inside the template.

**Why:** Twig is for presentation, and it runs logic slower than PHP. Rules buried in a template cannot be unit-tested and get copied across views. A view DTO keeps the template readable and the logic in one tested place. See architecture/always-thin-controllers.

**Good / Bad:**
```twig
{# Bad: the template filters and counts. #}
{% set published = 0 %}
{% for page in book.pages if page.isPublished %}
  {% set published = published + 1 %}
{% endfor %}
<p>{{ published }} pages publiees</p>

{# Good: PHP did the work, Twig just prints. #}
<p>{{ view.publishedPageCount }} pages publiees</p>
```

**See also:** architecture/always-thin-controllers, architecture/always-use-dtos-at-boundaries.

**Enforced by:** review.
