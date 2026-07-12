---
name: always-implement-factories
description: Build complex objects through factories or named constructors so their invariants hold the moment they exist.
paths: ["src/**/*.php"]
severity: should
---
# Always implement factories for complex objects

**Rule:** Construct objects with real invariants (Invoice, RoleAssignment, Account) through a factory service or a named constructor, not `new` plus a chain of setters. Pass every required value in one call so an object is never half-built or invalid.

**Why:** encapsulation and always-valid objects. A bare constructor with public setters lets callers create an Invoice with no number or a RoleAssignment with no scope. A named constructor or factory makes the valid creation path the only path.

**Good / Bad:**
```php
// Bad - eight setters, any of them skippable, no guarantee of validity
$invoice = new Invoice();
$invoice->setOrganization($org);
$invoice->setNumber($number);
// ... five more, easy to forget one

// Good - a named constructor that enforces the invariants
final class Invoice
{
    public static function issue(Organization $org, InvoiceNumber $number, Money $total): self
    {
        if ($total->isNegative()) {
            throw new \InvalidArgumentException('Invoice total cannot be negative.');
        }
        $invoice = new self();
        $invoice->organization = $org;
        $invoice->number = $number;
        $invoice->total = $total;
        return $invoice;
    }
}
```

**See also:** [[never-put-business-logic-in-entities]] (the factory sets state; richer rules live in a service).

**Enforced by:** review + PHPStan 9 (private constructor where a named constructor is the entry point).
