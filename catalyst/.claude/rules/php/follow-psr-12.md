---
name: follow-psr-12
description: Follow PSR-12 / PER coding style and let php-cs-fixer format the code, never hand-format.
paths: ["**/*.php"]
severity: must
---
# Follow PSR-12 coding style

**Rule:** Write code to PSR-12 (and the PER Coding Style that supersedes it): one class per file, braces on their own line for classes and methods, ordered and grouped `use` imports, four-space indent. Do not argue about formatting in review and do not hand-align: run php-cs-fixer and accept its output.

**Why:** PSR-12 / PER is the agreed PHP-FIG standard, the same one Symfony itself ships. A single mechanical formatter means diffs show real changes, not whitespace noise, and nobody spends a review cycle on brace placement.

**Good / Bad:**
```php
// Bad: opening brace on the same line, unsorted imports, two statements per line.
use App\Entity\User; use App\Repository\OrgRepository;
class InvoiceService {
    public function issue(): void { $this->seal(); $this->send(); }
}
```
```php
// Good: PER layout the fixer produces.
use App\Entity\User;
use App\Repository\OrgRepository;

class InvoiceService
{
    public function issue(): void
    {
        $this->seal();
        $this->send();
    }
}
```

**Enforced by:** php-cs-fixer (`@PSR12` / `@PER-CS` rulesets) in `.githooks` and CI; it auto-fixes locally, the CI run is check-only.
