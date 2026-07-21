---
name: never-weaken-a-failing-test
description: When a test fails, fix the code or a genuinely wrong assertion; never loosen, delete, or skip the test to force it green.
severity: must
---
# Never weaken a failing test

**Rule:** A red test means the code is wrong or the assertion was genuinely wrong. Fix one of those. Never loosen an assertion, delete a case, or skip it (PHPUnit `markTestSkipped`, pytest `skip`) to dodge a real failure. A bug fix begins with a failing regression test that then goes green, see [[always-test-critical-paths]].

**Why:** Weakening a test throws away the signal you wrote it to keep, so the regression ships anyway. Testing only works when red genuinely means broken; a skipped case or a vague assertion is a silent hole.

**Good / Bad:**
```php
// Bad: silence the failure by loosening the assertion.
- self::assertSame(3, $book->getPageCount());
+ self::assertGreaterThan(0, $book->getPageCount()); // "passes" while the bug stays

// Good: keep the exact assertion, fix the code that miscounts.
self::assertSame(3, $book->getPageCount());
```
```python
# Bad: loosen the exact probability to dodge a real regression.
- assert dice.p_at_least(base, 20) == Fraction(1, 2)
+ assert dice.p_at_least(base, 20) > 0  # green and meaningless

# Good: keep the exact value, fix the math that drifted.
assert dice.p_at_least(base, 20) == Fraction(1, 2)
```

**See also:** [[always-test-critical-paths]], [[assert-exact-golden-values]].

**Enforced by:** review (a diff that loosens or skips a test instead of fixing the cause is rejected).
