---
name: always-assess-cost-impact
description: Before adding infra or third-party usage, state the cost and quota impact and cap or batch anything unbounded.
paths: ["**"]
severity: should
---
# Always assess cost impact
**Rule:** Before adding infra or third-party usage, state the cost and quota impact: object-storage egress, email volume, PDP and Mollie API calls, Sentry events. Cap or batch anything unbounded (per-visitor writes, per-row external calls) and name the ceiling. This is a required item on the spec checklist.

**Why:** Spec-first means cost is part of the spec, not a surprise on the bill. An unbounded per-row external call scales with data and quietly blows a quota or a rate limit. The canonical "Cost" item lives in `.claude/skills/athletis-spec/references/spec-checklist.md`.

**Good / Bad:**
```php
// Bad: one external API call per row, unbounded as the table grows.
foreach ($rows as $row) { $pdp->submit($row); }

// Good: batch with a named ceiling.
foreach (array_chunk($rows, 100) as $batch) { $pdp->submitBatch($batch); } // max 100 per call
```

See `http-and-caching/cache-expensive-reads-in-redis` for cutting repeat cost on the read path.

**Enforced by:** review (the spec checklist Cost item must be answered, not blank).
