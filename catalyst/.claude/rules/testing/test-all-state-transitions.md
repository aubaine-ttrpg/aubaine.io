---
name: test-all-state-transitions
description: For stateful things, test every valid transition works and every invalid one is blocked.
paths: ["tests/**", "src/**"]
severity: should
---
# Test all state transitions

**Rule:** For anything stateful (client lead→active→paused→ended→archived; payment pending→paid/late; invoice draft→issued) test that every legal transition works and every illegal one is rejected, for example issued→edited must be impossible. Model the states as enums per php/prefer-enums-over-constants, and the issued-invoice block enforces invoicing/never-mutate-an-issued-invoice.

**Why:** A status field is only safe if illegal moves are actually refused, not merely undisplayed in the UI. State-transition testing in `tests/TEST_DESIGN.md` requires proving both directions: the legal path and the rejection.

**Good / Bad:**
```php
// Bad: only the happy transition is checked.
public function testDraftCanBeIssued(): void { /* draft → issued */ }

// Good: also prove illegal transitions are rejected.
public function testIssuedInvoiceCannotBeEdited(): void
{
    $invoice = $this->issuedInvoice();
    $this->expectException(\DomainException::class);
    $invoice->setAmount(1_00); // issued → edited must fail
}
```

**Enforced by:** test (a case per legal transition and per blocked transition) + review.
