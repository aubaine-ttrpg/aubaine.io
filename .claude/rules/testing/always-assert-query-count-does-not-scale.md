---
name: always-assert-query-count-does-not-scale
description: Catalyst list views assert the SQL query count is constant in row count so N+1 cannot regress.
paths: ["catalyst/**"]
severity: must
---
# Always assert query count does not scale

**Rule:** Once catalyst runs on Doctrine (the SQLite migration), every list or collection view gets a test that asserts the SQL query count stays constant as rows grow, using a query-count assertion or the Doctrine SQL logger. Tie this to database/never-cause-n-plus-one: the code avoids N+1, this test proves it cannot come back.

**Why:** An view that fires one query per row works fine with three books in dev and falls over on a real library; only an explicit assertion catches the regression. This is the performance leg of the non-functional category, see [[always-cover-the-five-categories]].

**Good / Bad:**
```php
// Bad: assert the payload only, the query count free to explode.
self::assertCount(10, $response['books']);

// Good: 10 books cost the same query count as 1.
$one = $this->queriesFor(fn () => $this->listBooks(seed: 1));
$ten = $this->queriesFor(fn () => $this->listBooks(seed: 10));
self::assertSame($one, $ten, 'book list must not scale queries with rows');
```

**See also:** [[always-test-performance]], [[always-cover-the-five-categories]], database/never-cause-n-plus-one.

**Enforced by:** PHPUnit (a query-count assertion that does not scale with row count) + review.
