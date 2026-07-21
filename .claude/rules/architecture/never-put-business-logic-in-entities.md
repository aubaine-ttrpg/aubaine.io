---
name: never-put-business-logic-in-entities
description: Entities hold state and simple invariants only; business rules, queries, and external calls belong in services.
paths: ["catalyst/src/Entity/**", "catalyst/src/**/Entity/**"]
severity: should
---
# Never put business logic in entities

**Rule:** An entity keeps its own state and small invariants (a setter that rejects an empty title). It does not run business processes. No `EntityManager` or repository inside an entity, no `flush`, no PDF renderer or HTTP client, no queries. Those live in services. Entities stay close to anemic, behavior limited to guarding their own fields.

**Why:** SRP: business logic belongs in services, not entities. An entity that fetches related rows or renders a PDF cannot be constructed in a test without a database and hides side effects behind a getter. Keep persistence at the service edge.

**Good / Bad:**
```php
// Bad - the entity reaches into persistence and the outside world
class Book
{
    public function publish(EntityManagerInterface $em, PdfRenderer $renderer): void
    {
        $this->status = Status::Published;
        $em->flush();               // no
        $renderer->render($this);   // no
    }
}

// Good - entity guards state; the service orchestrates
class Book
{
    public function markPublished(\DateTimeImmutable $on): void
    {
        if ($this->status === Status::Published) {
            throw new \DomainException('Book already published.');
        }
        $this->status = Status::Published;
        $this->publishedAt = $on;
    }
}
```

**See also:** [[always-thin-controllers]], [[always-implement-factories]].

**Enforced by:** PHPStan 9 (no service or `EntityManagerInterface` parameters in `catalyst/src/Entity`) + review.
