---
name: never-create-a-form-without-csrf
description: Every state-changing form and POST/PUT/PATCH/DELETE action must carry and validate a CSRF token.
paths: ["catalyst/src/**/Controller/**", "catalyst/src/Form/**", "catalyst/templates/**/*.html.twig"]
severity: must
---
# Never create a form without CSRF protection

**Rule:** Every state-changing request (POST/PUT/PATCH/DELETE) carries a CSRF token that the server validates. Symfony Form types do this for you. For hand-rolled forms and link-style deletes, add the token explicitly and check it before acting.

**Why:** OWASP Top 10 A01 (Broken Access Control) / CSRF. Catalyst runs locally with no login, but another page open in the same browser can still POST to its address and trigger a state change, deleting a Page or overwriting a Book, using nothing but the request itself. A CSRF token proves the request came from catalyst's own form and not a foreign page.

**Good / Bad:**
```twig
{# Bad:  a delete link with no token #}
<a href="{{ path('page_delete', {id: page.id}) }}">Supprimer</a>

{# Good:  POST form with the framework CSRF field #}
<form method="post" action="{{ path('page_delete', {id: page.id}) }}">
    <input type="hidden" name="_token" value="{{ csrf_token('delete-page-' ~ page.id) }}">
    <button type="submit">Supprimer</button>
</form>
```
```php
// Good:  controller verifies before mutating
if (!$this->isCsrfTokenValid('delete-page-'.$page->getId(), $request->request->getString('_token'))) {
    throw $this->createAccessDeniedException();
}
```

**See also:** [[always-validate-input-server-side]], [[always-set-security-headers-and-csp]].

**Enforced by:** Symfony native (Form component `csrf_protection`) + review + functional test (a POST without a valid token returns 403).
