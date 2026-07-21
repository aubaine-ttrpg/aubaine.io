---
name: never-flush-in-request-subscribers
description: Event subscribers and lifecycle listeners on the request path never call flush; the controller or service owns one flush.
paths: ["catalyst/**"]
severity: should
---
# Never flush in request subscribers

**Rule:** Do not call `EntityManager::flush()` from a kernel event subscriber or listener (`kernel.request`, `kernel.controller`, `kernel.response`), from a Doctrine lifecycle listener (`postLoad`, `onFlush`), or from anything that runs on every request. Change managed entities there if you must, but let the controller or an application service own a single `flush()` at the end of the unit of work. One request, one deliberate flush, at a place you can see.

**Why:** `flush()` commits the whole managed unit of work, not just the entity in front of you. A flush hidden in a request subscriber writes whatever else happens to be pending, runs at an uncontrolled point in the request, can fire on a read-only page, and re-entering it from a lifecycle callback risks a nested flush. Doctrine's guidance is one flush per request at a known boundary, which keeps writes predictable and lets a test assert exactly when they happen.

**Good / Bad:**
```php
// Bad: a subscriber stamps updatedAt and flushes on every request.
final class TouchBookSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {
        $book = $this->current();
        $book->touch(new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
        $this->em->flush();            // commits the whole UoW, every request
    }
}

// Good: the entity changes; the controller flushes once, on purpose.
public function rename(Book $book, string $title): Response
{
    $book->rename($title);             // in-memory change
    $book->touch($this->clock->now());
    $this->em->flush();                // single, controlled commit
    return $this->redirectToBook($book);
}
```

**See also:** architecture/always-thin-controllers, architecture/never-put-business-logic-in-entities, [[never-cause-n-plus-one]].

**Enforced by:** PHPStan (no `EntityManagerInterface::flush()` call under `catalyst/src/EventListener`) + review.
