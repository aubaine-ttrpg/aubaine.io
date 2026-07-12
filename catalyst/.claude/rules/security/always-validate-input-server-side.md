---
name: always-validate-input-server-side
description: Validate every input on the server with Symfony Validator constraints; client validation is UX only and never trusted.
paths: ["src/**/Controller/**", "src/Dto/**", "src/Entity/**", "src/Form/**"]
severity: must
---
# Always validate input server-side

**Rule:** Validate every incoming value on the server with Symfony Validator constraints on DTOs or entities. Treat client-side checks (HTML `required`, JS) as UX hints only, never as a trust boundary. On a validation failure return 422 with a problem document (see http-and-caching/use-correct-verbs-codes-and-problem-json).

**Why:** OWASP Top 10 A03 (Injection) and A04 (Insecure Design). Anything from the browser can be forged, including hidden fields, disabled inputs, and query params. ISO 27001 input-validation control. Server validation is the only check an attacker cannot bypass.

**Good / Bad:**
```php
// Bad:  trusting a hidden field and the browser's required attribute
$email = $request->request->getString('email'); // assumed valid because <input required>

// Good:  constraints checked on the server
final class CreateLeadDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';
}
// controller
$errors = $validator->validate($dto);
if (\count($errors) > 0) {
    return $this->json($errors, 422); // problem+json
}
```

**Enforced by:** Symfony native (Validator) + PHPStan 9 (typed DTOs) + functional test (forged/invalid payload returns 422).
