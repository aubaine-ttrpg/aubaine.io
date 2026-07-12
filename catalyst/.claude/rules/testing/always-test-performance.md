---
name: always-test-performance
description: Every data-touching endpoint ships performance tests asserting a response-time ceiling and stable query counts across data volumes, so degradation is caught in CI, not in production.
paths: ["src/Controller/**", "src/Repository/**", "src/MessageHandler/**", "src/Scheduler/**", "src/Command/**", "tests/**"]
severity: must
---
# Always test performance

**Rule:** Any endpoint or query that touches the database ships **performance tests**, not just
functional ones. Assert two things: a **response-time ceiling** (API / page / heavy, aligned with Core
Web Vitals) and that the **SQL query count stays flat as row count grows** (run the scenario at several
volume tiers and assert the count does not scale). Treat a breached ceiling or a scaling query count as
a failing test, exactly like a wrong result.

**Why:** Functional tests pass on 3 rows and hide the N+1 that melts at 10,000. As a solo team we cannot
babysit production, so the suite has to catch degradation before merge. This is the executable form of
the `performance` cross-cutting checklist item. It extends
[[always-assert-query-count-does-not-scale]] (the N+1 guard) with explicit latency ceilings, and the
mechanics live in [`tests/CONVENTIONS.md`](../../../tests/CONVENTIONS.md) (thresholds, volume tiers,
`assertQueryCountDoesNotScale`).

**Good / Bad:**
```php
// Bad: only correctness on a tiny fixture; N+1 and slow pages slip through.
public function testClientList(): void { $this->assertResponseIsSuccessful(); }

// Good: latency ceiling + query count proven flat across volumes.
#[Group('performance')]
public function testClientListScales(): void {
    $this->assertQueryCountDoesNotScale(fn (int $n) => $this->seedClients($n), ['/clients']);

    $start = microtime(true);
    static::createClient()->request('GET', '/clients');
    $this->assertResponseTimeUnderCeiling(microtime(true) - $start, PerformanceThresholds::MAX_RESPONSE_TIME_PAGE);
}
```

**See also:** [[always-assert-query-count-does-not-scale]], database/never-cause-n-plus-1, http-and-caching/cache-expensive-reads-in-redis.

**Enforced by:** test (`#[Group('performance')]`), CI.
