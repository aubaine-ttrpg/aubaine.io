---
name: treat-ai-output-as-untrusted-until-integrated
description: Generated code is untrusted until verified to fit the system; it must reuse existing domain types and patterns, add no second way to do a thing, and preserve isolation, idempotency, and security.
severity: must
---
# Treat AI output as untrusted until integrated

**Rule:** Generated code is locally plausible but often globally wrong; treat it as untrusted until you verify it fits the system. It must reuse the existing domain model, services, DTOs, voters, repositories, messages, and templates; follow nearby naming, error-handling, transaction, and logging patterns; add no second way to do something the app already does; and preserve idempotency and security. Compiling is not review.

**Why:** An agent that did not search deeply enough produces a change that builds and passes a shallow test yet introduces a parallel path, a competing business rule, or a broken invariant. Checking that it fits the existing system is the difference between integration and accumulation.

**Good / Bad:**
```php
// Bad: a fresh query that re-derives how a book's pages are ordered, untrusted and unintegrated.
$rows = $conn->fetchAllAssociative('SELECT * FROM page WHERE book_id = ? ORDER BY position', [$bookId]);

// Good: reuse the existing repository; page ordering lives in one place.
$pages = $this->pageRepository->findOrderedForBook($bookId);
```

**See also:** [[search-before-create]], architecture/keep-it-simple.

**Enforced by:** review, PHPStan 9.
