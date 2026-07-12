---
name: always-factorize
description: Keep one canonical home for each piece of logic; extract anything duplicated into a shared service, trait, or method.
paths: "{src,templates}/**/*{.php,html.twig}"
severity: should
---
# Always factorize shared logic

**Rule:** When the same logic appears twice, give it one home (a service method, a private method, a Twig component, a trait) and call it from both places. Copying a third time is the signal you already missed the first extraction.

**Why:** DRY. Duplicated logic drifts: a fix lands in one copy and not the other. This applies to docs too, see [[process/never-duplicate-reference-living-files]] (one canonical home, everything else links).

**Good / Bad:**
```php
// Bad - the same VAT math pasted into two services
$ttc = $ht * (1 + 0.20); // in InvoiceIssuer
$ttc = $ht * (1 + 0.20); // again in QuoteBuilder

// Good - one home, both callers depend on it
final class VatCalculator
{
    public function inclusive(Money $ht, VatRate $rate): Money
    {
        return $ht->multiply(1 + $rate->asFloat());
    }
}
```

**See also:** process/never-duplicate-reference-living-files for the same principle across docs and rules.

**Enforced by:** review + PHPStan 9 (and your judgment: prefer one good shared method over three drifting copies).
