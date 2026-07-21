---
name: use-traits-deliberately
description: Use a trait only for genuinely cross-cutting, stateless-or-self-contained behaviour reused across unrelated classes; never to share business logic or mutable state.
paths: ["catalyst/src/**", "catalyst/tests/**"]
severity: should
---
# Use traits deliberately

**Rule:** Reach for a PHP `trait` only when the same **cross-cutting, self-contained** behaviour is
needed by **several unrelated classes** that cannot share a base type, and the behaviour does not carry
business logic. Good fits: Gedmo/entity mapping concerns (timestamps, soft-delete, slug, blameable),
a reusable test assertion set. Do **not** use a trait to share
domain logic between services (inject a collaborator instead), to smuggle in mutable shared state, or to
avoid designing a proper class. Create a new trait only after the second real reuse, not before.

**Why:** Traits are horizontal copy-paste resolved by the compiler: they create hidden coupling, no
clear ownership, and an explosion of test surface when they hold logic or state. Composition keeps
behaviour testable and dependencies explicit ([[prefer-composition-over-inheritance]],
[[always-inject-dependencies]]). Entity behaviour traits are the sanctioned exception because they are
declarative mapping, not logic. Pairs with [[keep-it-simple]] and [[always-factorize]].

**Good / Bad:**
```php
// Good: a declarative, cross-cutting entity concern reused by many tables.
trait TimestampableTrait {
    #[ORM\Column] private \DateTimeImmutable $createdAt;
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}

// Bad: business logic + state shared through a trait; inject a service instead.
trait ExportTrait {
    private string $format = 'json';         // shared mutable state, no owner
    public function export(): string { /* domain logic hidden in a mixin */ }
}
```

**See also:** [[prefer-composition-over-inheritance]], [[always-inject-dependencies]].

**Enforced by:** review.
