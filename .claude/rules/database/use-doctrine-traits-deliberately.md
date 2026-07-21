---
name: use-doctrine-traits-deliberately
description: Use small mapping traits only for genuine cross-entity cross-cutting state, never to share domain behavior.
paths: ["catalyst/**"]
severity: should
---
# Use Doctrine traits deliberately

**Rule:** A Doctrine mapping trait carries only declarative, cross-cutting persistence state that unrelated entities genuinely share: an audit stamp (`created_at` / `updated_at`), a generated uid. It maps columns and exposes plain accessors, nothing else. Do not put domain behaviour, queries, `EntityManager` access, or a business rule in a trait; when several entities need the same behaviour, inject a collaborator instead. Add the trait only after a second entity really needs the same mapped state (the day a `SkillTree` needs the timestamps a `Book` already has), not in anticipation.

**Why:** A trait is compile-time copy-paste with no owner. Kept to declarative mapping it is harmless and removes duplicated column definitions; the moment it holds behaviour or mutable state it hides coupling and multiplies the test surface, with no clear home for the logic. Timestamp and uid mapping is the sanctioned exception for the same reason given in architecture/use-traits-deliberately: it is pure ORM declaration, not logic. Business rules belong in a service (architecture/never-put-business-logic-in-entities).

**Good / Bad:**
```php
// Good: a declarative audit stamp reused by unrelated entities.
trait TimestampsTrait
{
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $updatedAt;

    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}

// Bad: domain behaviour and a flush smuggled into a mixin; no owner, untestable in isolation.
trait PublishableTrait
{
    private bool $published = false;

    public function publish(EntityManagerInterface $em, PdfRenderer $renderer): void
    {
        $this->published = true;      // domain rule hidden in a trait
        $renderer->render($this);     // external call from a mapping mixin
        $em->flush();                 // and a flush, see never-flush-in-request-subscribers
    }
}
```

**See also:** architecture/use-traits-deliberately, architecture/never-put-business-logic-in-entities, [[follow-column-and-table-naming]], php/always-use-immutable-utc-datetimes.

**Enforced by:** PHPStan 9 (a mapping trait declares no service or `EntityManagerInterface` dependency) + review.
