---
name: treat-ai-output-as-untrusted-until-integrated
description: Generated code is untrusted until verified to fit the system; it must reuse existing domain types and patterns, add no second way to do a thing, and preserve isolation, idempotency, and security.
severity: must
---
# Treat AI output as untrusted until integrated

**Rule:** Generated code is locally plausible but often globally wrong; treat it as untrusted until you verify it fits the system. It must reuse the existing domain model, services, DTOs, voters, repositories, messages, and templates; follow nearby naming, error-handling, transaction, and logging patterns; add no second way to do something the app already does; and preserve tenant isolation, idempotency, and security. Compiling is not review.

**Why:** REVIEW_DOCTRINE §16.2. An agent that did not search deeply enough produces a change that builds and passes a shallow test yet introduces a parallel path, a competing business rule, or a broken invariant. Checking that it fits the existing system is the difference between integration and accumulation.

**Good / Bad:**
```php
// Bad: a fresh repository method that re-derives tenant scope, untrusted and unintegrated.
$rows = $conn->fetchAllAssociative('SELECT * FROM booking WHERE org_id = ?', [$orgId]);

// Good: reuse the existing repository; the SQLFilter + RLS already scope the active org.
$bookings = $this->bookingRepository->findUpcomingForActiveOrg();
```

**See also:** [[search-before-create]], database/always-enforce-tenant-isolation.

**Enforced by:** review, PHPStan 9, tenant-isolation test.
