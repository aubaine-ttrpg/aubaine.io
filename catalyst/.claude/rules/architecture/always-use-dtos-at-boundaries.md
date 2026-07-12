---
name: always-use-dtos-at-boundaries
description: Cross controller-to-service and form-to-domain boundaries with typed DTOs or command objects, never raw arrays or the Request.
paths: ["src/**/Controller/**", "src/Form/**", "src/**/Command/**", "src/**/Dto/**"]
severity: should
---
# Always use DTOs at boundaries

**Rule:** Pass a typed DTO or command object across each boundary: controller to service, form to domain. Never hand a service the raw `Request`, `$request->request->all()`, or a loose associative array. The DTO names every field and its type.

**Why:** a typed DTO at the boundary documents the contract, gives PHPStan something to check, and keeps services independent of HTTP. Symfony maps request bodies straight into a DTO with `#[MapRequestPayload]` and validates it with constraints.

**Good / Bad:**
```php
// Bad - the service must know HTTP and guess at keys
public function issue(Request $request): Invoice
{
    $orgId = $request->request->get('org'); // string? int? null?
}

// Good - a validated command object, no HTTP knowledge
final readonly class IssueInvoice
{
    public function __construct(
        #[Assert\Uuid] public string $organizationId,
        #[Assert\Positive] public int $amountCents,
    ) {}
}
public function issue(IssueInvoice $command): Invoice { /* ... */ }
```

**See also:** [[always-thin-controllers]] (the controller fills the DTO and hands it over).

**Enforced by:** PHPStan 9 + review + Symfony native (`#[MapRequestPayload]` validation).
