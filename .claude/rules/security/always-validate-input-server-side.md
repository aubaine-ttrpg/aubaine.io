---
name: always-validate-input-server-side
description: Validate every input on the server with Symfony Validator constraints; client validation is UX only and never trusted.
paths: ["catalyst/src/**/Controller/**", "catalyst/src/Dto/**", "catalyst/src/Entity/**", "catalyst/src/Form/**"]
severity: must
---
# Always validate input server-side

**Rule:** Validate every incoming value on the server with Symfony Validator constraints on DTOs or entities. Treat client-side checks (HTML `required`, JS) as UX hints only, never as a trust boundary. On a validation failure return 422 with a problem document (see http-and-caching/use-correct-verbs-codes-and-problem-json).

**Why:** OWASP Top 10 A03 (Injection) and A04 (Insecure Design). Anything from the browser can be forged, including hidden fields, disabled inputs, and query params, and the Live Components that drive the editor round-trip state through the client. Server validation is the only check a forged request cannot bypass.

**Good / Bad:**
```php
// Bad:  trusting a hidden field and the browser's required attribute
$title = $request->request->getString('title'); // assumed valid because <input required>

// Good:  constraints checked on the server
final class SavePageDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    public string $title = '';

    #[Assert\Regex('/^[a-z0-9-]+$/')]
    public string $slug = '';
}
// controller
$errors = $validator->validate($dto);
if (\count($errors) > 0) {
    return $this->json($errors, 422); // problem+json
}
```

**See also:** [[always-sanitize-user-html]], [[never-create-a-form-without-csrf]].

**Enforced by:** Symfony native (Validator) + PHPStan 9 (typed DTOs) + functional test (forged/invalid payload returns 422).
