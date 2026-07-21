---
name: always-pass-phpstan-level-9
description: All code must pass PHPStan at level 9 with no new baseline entries and no unjustified ignores.
paths: ["catalyst/src/**/*.php", "catalyst/tests/**/*.php"]
severity: must
---
# Always pass PHPStan level 9

**Rule:** New and changed code passes PHPStan at level 9 (the maximum). Do not grow the baseline for new code (the baseline is a debt ledger for legacy only). Every `@phpstan-ignore` carries a one-line justification comment explaining why it is safe.

**Why:** Level 9 is PHPStan's strictest setting: it treats `mixed` as unsafe and forces you to narrow nullables and unions before use. That is what catches the null-deref and wrong-type bugs that strict types alone (see [[always-declare-strict-types]]) cannot. A silent baseline entry or bare ignore just defers the bug.

**Good / Bad:**
```php
// Bad: $book may be null, getId() can blow up at runtime.
$book = $this->repo->find($id);
return $book->getId();
```
```php
// Good: narrowed first, type checker and runtime agree.
$book = $this->repo->find($id);
if (null === $book) {
    throw $this->createNotFoundException();
}
return $book->getId();
```

**Enforced by:** PHPStan 9 in CI and in `.githooks` (the quality gate fails the build on any new error or baseline addition).
