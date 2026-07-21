---
name: always-use-dtos-at-boundaries
description: Cross controller-to-service and form-to-domain boundaries with typed DTOs or command objects, never raw arrays or the Request.
paths: ["catalyst/src/**/Controller/**", "catalyst/src/Form/**", "catalyst/src/**/Command/**", "catalyst/src/**/Dto/**"]
severity: should
---
# Always use DTOs at boundaries

**Rule:** Pass a typed DTO or command object across each boundary: controller to service, form to domain. Never hand a service the raw `Request`, `$request->request->all()`, or a loose associative array. The DTO names every field and its type.

**Why:** a typed DTO at the boundary documents the contract, gives PHPStan something to check, and keeps services independent of HTTP. Symfony maps request bodies straight into a DTO with `#[MapRequestPayload]` and validates it with constraints.

**Good / Bad:**
```php
// Bad - the service must know HTTP and guess at keys
public function add(Request $request): Page
{
    $bookId = $request->request->get('book'); // string? int? null?
}

// Good - a validated command object, no HTTP knowledge
final readonly class AddPage
{
    public function __construct(
        #[Assert\Uuid] public string $bookId,
        #[Assert\NotBlank] public string $title,
    ) {}
}
public function add(AddPage $command): Page { /* ... */ }
```

**See also:** [[always-thin-controllers]] (the controller fills the DTO and hands it over).

**Enforced by:** PHPStan 9 + review + Symfony native (`#[MapRequestPayload]` validation).
