---
name: never-weaken-tools-to-pass
description: Never make generated code pass by weakening tools; no lowering static-analysis or CI strictness, no broad ignores, no widening types to mixed, no deleting tests, no side-effect config changes.
severity: must
---
# Never weaken tools to pass

**Rule:** Never make generated code pass by weakening the tools. No lowering PHPStan, Psalm, ESLint, PHP-CS-Fixer, or CI strictness; no expanding static-analysis baselines; no broad ignore rules or `@phpstan-ignore`; no widening precise types to `mixed`, `array`, or `object` to silence errors; no loosening assertions; no deleting tests instead of fixing behaviour; and no changing CI, Docker, Composer, npm, or Symfony config as a side effect of an application patch. Code adapts to the quality bar; the bar never drops to accept code. Suppressions are acceptable only when narrow, explained, and safer than the alternative.

**Why:** REVIEW_DOCTRINE §16.11. AI-generated patches often make tools pass by weakening the tools instead of fixing the code. The repository's quality bar is the thing protecting it; quietly lowering it to admit generated code defeats the entire static-analysis and test investment.

**Good / Bad:**
```php
// Bad: widen the type and suppress to make PHPStan green.
/** @param mixed $amount */
public function charge($amount): void {} // @phpstan-ignore-line

// Good: keep the precise type; the code adapts to the bar, not the bar to the code.
public function charge(Money $amount): void {}
```

**See also:** php/always-pass-phpstan-level-9, testing/never-weaken-a-failing-test.

**Enforced by:** PHPStan 9 + CI strictness, php-cs-fixer, review of baseline/config diffs.
