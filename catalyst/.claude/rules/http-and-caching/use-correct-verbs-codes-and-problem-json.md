---
name: use-correct-verbs-codes-and-problem-json
description: Use the correct HTTP verb and status code, and return JSON errors as application/problem+json.
paths: ["src/Controller/**/*.php", "src/**/*Controller.php", "config/routes/**"]
severity: must
---
# Use correct verbs, codes, and problem+json

**Rule:** Match the verb to the intent (GET safe and idempotent, POST to create, PUT/PATCH to update, DELETE to remove) and the status to the outcome: 200/201/204 on success, 422 on validation failure, 403/404 for authorization or missing resources, 409 on conflict. Never return a 500 page for a validation error and never send `200` with an error body. Serialize JSON errors as `application/problem+json`.

**Why:** RFC 9110 defines HTTP semantics, so clients and caches rely on the status line, not the body, to decide what happened. RFC 9457 (problem+json) gives errors a machine-readable shape instead of ad-hoc payloads.

**Good / Bad:**
```php
// Bad: lies with a 200 and a hand-rolled error body.
return $this->json(['error' => 'email is invalid'], 200);

// Good: 422 with a problem+json document.
return $this->json(
    ['type' => 'about:blank', 'title' => 'Validation failed', 'status' => 422,
     'errors' => ['email' => 'invalid']],
    422,
    ['Content-Type' => 'application/problem+json'],
);
```

**See also:** security/always-validate-input-server-side.

**Enforced by:** review + functional test (assert status code and Content-Type).
