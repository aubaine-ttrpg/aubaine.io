---
name: never-use-float-for-money
description: Represent money as integer minor units (e.g. *_cents) with a currency, or a dedicated Money value object; never float or double.
paths: ["**/*.php"]
severity: must
---
# Never use float for money

**Rule:** Store and compute every monetary amount as **integer minor units** (a `*_cents` column and property) paired with an explicit currency, or wrap both in a dedicated `Money` value object. Never use `float`, `double`, or untyped decimals for an amount, and never do arithmetic on amounts in different currencies without converting through a defined rate.

**Why:** Floating point cannot represent most decimal amounts exactly, so `0.1 + 0.2` drifts and accumulated rounding corrupts totals, taxes, and balances. That is fatal for billing and invoicing: Mollie expects exact amounts and EN 16931 / Factur-X require precise, auditable line and total values (REVIEW_DOCTRINE §10.1, §10.9). Integer minor units make every operation exact and the currency explicit prevents silent mixed-currency math.

**Good / Bad:**
```php
// Bad: float loses precision and carries no currency.
private float $amount = 19.99;            // 19.99 is not exactly representable

// Good: integer minor units plus an explicit currency.
#[ORM\Column]
private int $amountHtCents = 1999;        // 19,99 EUR, exact
#[ORM\Column(length: 3)]
private string $currency = 'EUR';         // or wrap both in a Money value object
```

**See also:** database/follow-column-and-table-naming, payments/model-payment-states-explicitly, invoicing/always-sequential-gapless-numbering.

**Enforced by:** PHPStan 9, review.
