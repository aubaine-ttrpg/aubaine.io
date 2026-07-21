---
name: always-assess-cost-impact
description: Before adding infra or third-party usage, state the cost and quota impact and cap or batch anything unbounded.
paths: ["**"]
severity: should
---
# Always assess cost impact
**Rule:** Before adding a third-party call, a PDF render pass, or a bulk file operation, state its cost and quota impact: external http-client requests, PDF generation time, and per-entity reads or writes over the content export. Cap or batch anything unbounded (a call or render per Page, per Book, per row) and name the ceiling.

**Why:** State cost before you add the dependency, not when a run stalls or a rate limit trips. An unbounded per-row external call or render scales with the data and quietly blows a quota; naming the ceiling up front keeps it bounded as a Book grows.

**Good / Bad:**
```php
// Bad: one http-client request per Page, unbounded as the Book grows.
foreach ($book->getPages() as $page) { $client->request('GET', $page->assetUrl()); }

// Good: batch with a named ceiling.
foreach (array_chunk($book->getPages(), 100) as $batch) { $client->fetchAll($batch); } // max 100 per request
```

See `catalyst/docs/adr/0001-book-version-stamp-and-pdf-cache` for caching the expensive PDF render instead of repeating it.

**Enforced by:** review (state the cost impact before adding a dependency or an unbounded loop).
