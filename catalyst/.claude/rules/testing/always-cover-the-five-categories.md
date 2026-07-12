---
name: always-cover-the-five-categories
description: Every data or access feature covers all five categories, happy, authorization/IDOR, negative/error, edge and non-functional.
paths: ["tests/**", "src/**"]
severity: must
---
# Always cover the five categories

**Rule:** Every data or access feature carries tests in all five categories: happy path (the pinned journey, see [[always-pin-the-canonical-journey]]), authorization/IDOR (security/always-prevent-idor plus a cross-org id → 403/404 and a tenant-isolation test, database/always-enforce-tenant-isolation), negative/error (correct status, application/problem+json, no PII leak, http-and-caching/use-correct-verbs-codes-and-problem-json), edge cases, and non-functional (accessibility and performance, see the accessibility rules and [[always-assert-query-count-does-not-scale]]).

**Why:** This is the umbrella over the other testing rules and the five categories in `tests/TEST_DESIGN.md`. IDOR is a real, common leak (OWASP), so authorization by id is never assumed; a happy-path-only PR is half-tested by definition.

**Good / Bad:**
```text
Bad: PR with one happy-path test for a client list.

Good: five tests for the client list.
1. happy: coach sees her clients
2. authz/IDOR: cross-org client id → 404; org A sees nothing of org B
3. negative: bad filter → 400 application/problem+json, no PII
4. edge: empty list, max+1 page
5. non-functional: axe passes, query count does not scale
```

**Enforced by:** review + CI (no feature merges missing a category).
