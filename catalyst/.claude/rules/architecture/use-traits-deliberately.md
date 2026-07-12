---
name: use-traits-deliberately
description: Use a trait only for genuinely cross-cutting, stateless-or-self-contained behaviour reused across unrelated classes; never to share business logic or mutable state.
paths: ["src/**", "tests/**"]
severity: should
---
# Use traits deliberately

**Rule:** Reach for a PHP `trait` only when the same **cross-cutting, self-contained** behaviour is
needed by **several unrelated classes** that cannot share a base type, and the behaviour does not carry
business logic. Good fits: Gedmo/entity mapping concerns (timestamps, soft-delete, slug, blameable),
a reusable test assertion set, an `organization_id` tenant-column trait. Do **not** use a trait to share
domain logic between services (inject a collaborator instead), to smuggle in mutable shared state, or to
avoid designing a proper class. Create a new trait only after the second real reuse, not before.

**Why:** Traits are horizontal copy-paste resolved by the compiler: they create hidden coupling, no
clear ownership, and an explosion of test surface when they hold logic or state. Composition keeps
behaviour testable and dependencies explicit ([[prefer-composition-over-inheritance]],
[[always-inject-dependencies]]). Entity behaviour traits are the sanctioned exception
(`database/always-use-gedmo-and-useful-traits`) because they are declarative mapping, not logic. Pairs
with [[keep-it-simple]] and [[always-factorize]].

**Good / Bad:**
```php
// Good: a declarative, cross-cutting entity concern reused by every tenant table.
trait OrganizationOwnedTrait {
    #[ORM\Column(type: 'uuid')] private Uuid $organizationId;
    public function getOrganizationId(): Uuid { return $this->organizationId; }
}

// Bad: business logic + state shared through a trait; inject a service instead.
trait PricingTrait {
    private float $discount = 0.0;           // shared mutable state, no owner
    public function applyDiscount(): void { /* domain logic hidden in a mixin */ }
}
```

**See also:** [[prefer-composition-over-inheritance]], [[always-inject-dependencies]], database/always-use-gedmo-and-useful-traits.

**Enforced by:** review.
