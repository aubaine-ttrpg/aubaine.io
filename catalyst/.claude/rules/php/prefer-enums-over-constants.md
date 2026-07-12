---
name: prefer-enums-over-constants
description: Model closed sets of values as PHP backed enums instead of class constants or magic strings.
paths: ["**/*.php"]
severity: prefer
---
# Prefer enums over constants

**Rule:** For any closed set of values (Role, PlanType, PaymentStatus, InvoiceState) use a PHP 8.1+ backed enum, not `const STATUS_* = '...'` strings or bare literals scattered through the code. Type-hint the enum so the value is checked, and use `match` so a missing case is caught: PHPStan flags a non-exhaustive `match`, and at runtime an unhandled case throws `\UnhandledMatchError`.

**Why:** A backed enum gives a real type (you cannot pass a typo), a `match` that PHPStan checks for exhaustiveness (a new case forces every switch to be revisited), and a single home for the domain vocabulary. This is the PHP 8.1 Enumerations RFC working as intended. It is also house policy: roles are an enum and the role-to-abilities map lives in code (see ADR 0014; Voters authorize on abilities, not role-name strings). Pairs with testing/test-all-state-transitions for state machines like InvoiceState.

**Good / Bad:**
```php
// Bad: magic strings, no type safety, typos compile fine.
class Invoice
{
    public const STATUS_PAID = 'paid';
    public string $status = 'pakd'; // typo, nothing catches it
}
```
```php
// Good: backed enum, exhaustive match.
enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Refunded = 'refunded';
}

$label = match ($status) {
    PaymentStatus::Pending => 'En attente',
    PaymentStatus::Paid => 'Payé',
    PaymentStatus::Refunded => 'Remboursé',
}; // add a case and PHPStan flags this match until it is handled (an unhandled case throws \UnhandledMatchError at runtime)
```

**Enforced by:** review + PHPStan (it flags non-exhaustive `match` and wrong enum types). Doctrine maps backed enums natively via `enumType`.
