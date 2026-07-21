---
name: scrutinize-ai-coding-tells
description: Treat AI coding tells as review smells needing scrutiny, not auto-rejection; generic names, fake abstraction, defensive noise, hallucinated conventions, broad churn, mock-only tests, dependency creep.
severity: should
---
# Scrutinize AI coding tells

**Rule:** Treat AI coding tells as review smells that need scrutiny, not automatic rejection. Look hard at: generic names (`DataManager`, `HelperService`, `Processor`, `Enhanced*`, `Smart*`); fake abstraction (one-implementation interfaces, factories wrapping constructors, traits used to avoid ownership, "extensible" designs with no extension point); defensive noise (blanket null checks, broad `try/catch` that swallows, fallbacks that hide broken state); hallucinated conventions (invented env vars, service aliases, folders, CSS or Terraform patterns); broad unrelated churn; mock-only tests that prove only that mocks were called; shallow error messages; and dependency creep. The local codebase is the authority; prefer direct code, explicit invariants, and fail-fast behaviour.

**Why:** These patterns are how generic generated code reads; each is individually defensible but collectively signals that the change was not integrated. Naming them as smells lets a reviewer scrutinize the right places instead of either rubber-stamping or reflexively rejecting.

**Good / Bad:**
```php
// Bad: an "AI tell" stack: vague name, one-method interface of one, swallowed errors.
interface DataManagerInterface { public function handle(mixed $d): void; }
final class DataManager implements DataManagerInterface {
    public function handle(mixed $d): void { try { /* ... */ } catch (\Throwable) {} }
}

// Good: a named domain action, typed input, errors surfaced.
final class RegeneratePdf {
    public function __invoke(BookId $id): void { /* explicit, fail-fast */ }
}
```

**See also:** architecture/keep-it-simple, observability/never-swallow-exceptions, security/scan-dependencies.

**Enforced by:** review, PHPStan 9 (mixed, dead abstraction), observability rules.
