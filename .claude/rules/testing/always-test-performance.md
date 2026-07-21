---
name: always-test-performance
description: Every catalyst view or query that touches the database ships performance tests asserting a response-time ceiling and flat query counts across data volumes.
paths: ["catalyst/src/**", "catalyst/tests/**"]
severity: must
---
# Always test performance

**Rule:** Once catalyst runs on Doctrine, any view or query that touches the database ships **performance tests**, not just
functional ones. Assert two things: a **response-time ceiling** (view or heavy operation, aligned with Core
Web Vitals) and that the **SQL query count stays flat as row count grows** (run the scenario at several
volume tiers and assert the count does not scale). Treat a breached ceiling or a scaling query count as
a failing test, exactly like a wrong result.

**Why:** Functional tests pass on 3 rows and hide the N+1 that melts at 10,000. As a solo project we cannot
babysit production, so the suite has to catch degradation before merge. This extends
[[always-assert-query-count-does-not-scale]] (the N+1 guard) with explicit latency ceilings; keep the
thresholds and volume tiers as named constants in the test support so a ceiling is one authored value, not a
literal repeated across tests.

**Good / Bad:**
```php
// Bad: only correctness on a tiny fixture; N+1 and slow pages slip through.
public function testBookList(): void { $this->assertResponseIsSuccessful(); }

// Good: latency ceiling + query count proven flat across volumes.
#[Group('performance')]
public function testBookListScales(): void {
    $this->assertQueryCountDoesNotScale(fn (int $n) => $this->seedBooks($n), ['/books']);

    $start = microtime(true);
    static::createClient()->request('GET', '/books');
    $this->assertResponseTimeUnderCeiling(microtime(true) - $start, PerformanceThresholds::MAX_RESPONSE_TIME_PAGE);
}
```

**See also:** [[always-assert-query-count-does-not-scale]], database/never-cause-n-plus-one.

**Enforced by:** PHPUnit (`#[Group('performance')]`) + review.
