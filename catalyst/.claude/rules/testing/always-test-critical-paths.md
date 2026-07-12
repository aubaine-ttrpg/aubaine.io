---
name: always-test-critical-paths
description: Critical paths like auth, payments, invoicing, CSV import, tenant isolation and RGPD deletion always ship with automated tests.
paths: ["tests/**", "src/**"]
severity: must
---
# Always test critical paths

**Rule:** Authentication, payments, invoicing, CSV import, tenant isolation and RGPD deletion never ship without automated tests. No feature or fix is done until its tests are green (see tests/CONVENTIONS.md), and a bug fix starts with a failing regression test per [[never-weaken-a-failing-test]].

**Why:** These paths carry money, legal duties and cross-tenant data, so a silent regression is expensive and sometimes unlawful (RGPD, French e-invoicing). TDD/BDD makes the behaviour executable instead of hoped-for, and they sit at the top of the five categories in `tests/TEST_DESIGN.md`.

**Good / Bad:**
```php
// Bad: invoice issuing ships with no test, immutability unverified.
public function issue(Invoice $invoice): void { /* ... */ }

// Good: a functional test pins issue + immutability.
public function testIssuingInvoiceMakesItImmutable(): void
{
    $invoice = $this->issueInvoice();
    self::assertSame(InvoiceStatus::Issued, $invoice->getStatus());
    $this->expectException(\DomainException::class);
    $invoice->setAmount(999_00);
}
```

**Enforced by:** test + review (no merge of a critical path without covering tests).
