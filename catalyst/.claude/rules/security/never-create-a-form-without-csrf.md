---
name: never-create-a-form-without-csrf
description: Every state-changing form and POST/PUT/PATCH/DELETE action must carry and validate a CSRF token.
paths: ["src/**/Controller/**", "src/Form/**", "templates/**/*.html.twig"]
severity: must
---
# Never create a form without CSRF protection

**Rule:** Every state-changing request (POST/PUT/PATCH/DELETE) carries a CSRF token that the server validates. Symfony Form types do this for you. For hand-rolled forms and link-style deletes, add the token explicitly and check it before acting.

**Why:** OWASP Top 10 A01 (Broken Access Control) / CSRF. Without a token an attacker's page can make the coach's authenticated browser submit a request (delete a client, issue an invoice) using the session cookie. Cookies alone are not proof of intent. Pairs with [[always-set-secure-cookies]] (SameSite) as defense in depth.

**Good / Bad:**
```twig
{# Bad:  a logout/delete link with no token #}
<a href="{{ path('client_delete', {id: client.id}) }}">Supprimer</a>

{# Good:  POST form with the framework CSRF field #}
<form method="post" action="{{ path('client_delete', {id: client.id}) }}">
    <input type="hidden" name="_token" value="{{ csrf_token('delete-client-' ~ client.id) }}">
    <button type="submit">Supprimer</button>
</form>
```
```php
// Good:  controller verifies before mutating
if (!$this->isCsrfTokenValid('delete-client-'.$client->getId(), $request->request->getString('_token'))) {
    throw $this->createAccessDeniedException();
}
```

**Enforced by:** Symfony native (Form component `csrf_protection`) + review + functional test (a POST without a valid token returns 403).
