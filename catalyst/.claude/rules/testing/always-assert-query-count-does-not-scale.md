---
name: always-assert-query-count-does-not-scale
description: List and collection endpoints assert the SQL query count is O(1) in row count so N+1 cannot regress.
paths: ["tests/**", "src/**"]
severity: must
---
# Always assert query count does not scale

**Rule:** Every list or collection endpoint gets a test that asserts the SQL query count stays constant as rows grow, using a query-count assertion or the Doctrine SQL logger. Tie this to database/never-cause-n-plus-1: the code avoids N+1, this test proves it cannot come back.

**Why:** performance is the non-functional test category in `tests/TEST_DESIGN.md`. An endpoint that fires one query per row works fine with three rows in dev and falls over with a real tenant's data; only an explicit assertion catches the regression.

**Good / Bad:**
```php
// Bad: assert the payload only, query count free to explode.
self::assertCount(10, $response['clients']);

// Good: 10 clients cost the same query count as 1.
$one = $this->queriesFor(fn () => $this->listClients(seed: 1));
$ten = $this->queriesFor(fn () => $this->listClients(seed: 10));
self::assertSame($one, $ten, 'client list must not scale queries with rows');
```

**Enforced by:** test (query-count assertion that does not scale with row count) + CI.
