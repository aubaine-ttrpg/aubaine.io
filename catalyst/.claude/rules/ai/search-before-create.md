---
name: search-before-create
description: Before adding any new service, DTO, repository method, component, message, controller, module, or helper, search the codebase for an existing path and prove it; reject duplication under a different name.
severity: must
---
# Search before create

**Rule:** Before adding a new service, DTO, repository method, Twig/Live component, Stimulus controller, Messenger message, console command, Terraform module, CSS pattern, or helper, search the codebase for an existing path and prove it does not already exist. Reject duplication under a different name. Duplication is not only identical code: it includes repeated business rules, validation, authorization checks, payment transitions, tenant resolution, and query logic. New code is acceptable only when it has a distinct owner, a distinct responsibility, and a reason the existing path cannot safely support the behaviour.

**Why:** REVIEW_DOCTRINE §16.4 and §16.10. Agents create new code because they did not find the existing code, producing competing sources of truth that drift; flag such duplication as at least Major when it creates inconsistent validation, alternate authorization paths, or tenant-isolation risk.

**Good / Bad:**
```php
// Bad: a new "MoneyFormatter" added without grep; one already exists as a Money VO.
final class MoneyFormatter { public function format(int $cents): string { /* ... */ } }

// Good: grep first (rg "format.*cents|class Money"), then reuse the existing value object.
$label = $price->format(); // Money::format(), the single source of truth
```

**See also:** architecture/always-factorize, architecture/keep-it-simple.

**Enforced by:** review (grep evidence in the PR), the "one source of truth" check.
