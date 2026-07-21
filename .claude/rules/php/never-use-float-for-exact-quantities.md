---
name: never-use-float-for-exact-quantities
description: Represent any value that must be exact as integer units (minor units for currency, fixed-point for fractions) or a dedicated value object; never float or double.
paths: ["catalyst/**/*.php"]
severity: must
---
# Never use float for exact quantities

**Rule:** Store and compute every value that must be exact as **integer units**, or wrap it in a dedicated value object that owns its scale. A currency amount is integer minor units (a `*_cents` field) with an explicit currency, or a `Money` value object. A fractional quantity (a skill node cost, a weight) is a fixed-point integer (store `2.5` as `25` tenths), not a `float`. Never use `float`, `double`, or an untyped decimal for a value that has to add up, and never mix scales or currencies in one operation without converting through a defined rate.

**Why:** IEEE 754 binary floating point cannot represent most decimal fractions exactly, so `0.1 + 0.2` drifts and accumulated rounding corrupts any total, balance, or exported figure. catalyst writes the content JSON that the codex balancing lab treats as exact math, so a `float` that reads back as `2.4999999` poisons a downstream calculation with no error to catch it. Integer units make every operation exact, and a value object keeps the scale (and any currency) explicit so two different scales cannot be added by accident.

**Good / Bad:**
```php
// Bad: float drifts and carries no scale.
private float $cost = 2.5;                 // 2.5 is not exactly representable

// Good: exact integer units, scale named in the property.
#[ORM\Column]
private int $costTenths = 25;              // 2.5 stored as tenths, exact
// or a Money value object for currency: (int amountMinor, string currency)
```

**See also:** math/no-float-for-probability.

**Enforced by:** PHPStan 9, review.
