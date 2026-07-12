---
name: always-declare-strict-types
description: Every PHP file begins with declare(strict_types=1); on its first line.
severity: must
---
# Always declare strict types

**Rule:** Every PHP file in the repo opens with `declare(strict_types=1);` as the first statement, before the namespace. No exceptions for entities, configs, or fixtures.

**Why:** Without it PHP silently coerces scalars at call boundaries, so a string sneaks in where an int was promised and you find out in production, not in the type checker. Strict types turns that into a `TypeError` at the boundary. This is the PHP language behaviour for the `strict_types` declare directive, and the foundation [[always-pass-phpstan-level-9]] builds on.

**Good / Bad:**
```php
// Bad: no declare. "5" is coerced to 5, the bug hides.
<?php
namespace App\Billing;
function addCents(int $a, int $b): int { return $a + $b; }
addCents("5", 10); // works, returns 15, masks a real type error
```
```php
// Good: strict types, the coercion is rejected at the call site.
<?php
declare(strict_types=1);
namespace App\Billing;
function addCents(int $a, int $b): int { return $a + $b; }
addCents("5", 10); // TypeError: caught immediately, not in prod
```

**Enforced by:** php-cs-fixer (`declare_strict_types`) auto-fixes the header; PHPStan flags the coercions it prevents. Both run in `.githooks` and CI.
