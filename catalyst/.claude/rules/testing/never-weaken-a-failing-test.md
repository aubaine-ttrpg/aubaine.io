---
name: never-weaken-a-failing-test
description: When a test fails, fix the code or a genuinely wrong assertion, never loosen, delete or skip the test to force it green.
paths: ["tests/**", "src/**"]
severity: must
---
# Never weaken a failing test

**Rule:** A red test means the code is wrong or the assertion was genuinely wrong, fix one of those. Never loosen an assertion, delete a case or `markTestSkipped` to dodge a real failure. A bug fix begins with a failing regression test that then goes green, see [[always-test-critical-paths]] and tests/CONVENTIONS.md.

**Why:** Weakening a test throws away the signal you wrote it to keep, so the regression ships anyway. TDD only works when red genuinely means broken; a skipped or vague assertion is a silent hole.

**Good / Bad:**
```php
// Bad: silence the failure by loosening the assertion.
- self::assertSame(3, $count);
+ self::assertGreaterThan(0, $count); // "passes" while the bug stays

// Good: keep the exact assertion, fix the code that miscounts.
self::assertSame(3, $count);
```

**Enforced by:** review (a diff that loosens or skips a test instead of fixing the cause is rejected).
