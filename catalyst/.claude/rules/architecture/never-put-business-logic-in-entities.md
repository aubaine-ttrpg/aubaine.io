---
name: never-put-business-logic-in-entities
description: Entities hold state and simple invariants only; business rules, queries, and external calls belong in services.
paths: ["src/Entity/**", "src/**/Entity/**"]
severity: should
---
# Never put business logic in entities

**Rule:** An entity keeps its own state and small invariants (a setter that rejects a negative price). It does not run business processes. No `EntityManager` or repository inside an entity, no `flush`, no mailer or HTTP client, no queries. Those live in services. Entities stay close to anemic, behavior limited to guarding their own fields.

**Why:** SRP: business logic belongs in services, not entities. An entity that fetches related rows or sends mail cannot be constructed in a test without a database, hides side effects behind a getter, and ties to FrankenPHP worker hygiene (see [[database/never-flush-in-request-subscribers]]). Keep persistence at the service edge.

**Good / Bad:**
```php
// Bad - the entity reaches into persistence and the outside world
class Invoice
{
    public function markPaid(EntityManagerInterface $em, MailerInterface $mailer): void
    {
        $this->status = Status::Paid;
        $em->flush();                 // no
        $mailer->send(/* receipt */); // no
    }
}

// Good - entity guards state; the service orchestrates
class Invoice
{
    public function markPaid(\DateTimeImmutable $on): void
    {
        if ($this->status === Status::Paid) {
            throw new \DomainException('Invoice already paid.');
        }
        $this->status = Status::Paid;
        $this->paidAt = $on;
    }
}
```

**See also:** [[always-thin-controllers]], [[always-implement-factories]].

**Enforced by:** PHPStan 9 (no service or `EntityManagerInterface` parameters in `src/Entity`) + review.
