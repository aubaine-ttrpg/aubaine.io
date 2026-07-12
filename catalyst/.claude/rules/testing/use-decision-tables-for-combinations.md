---
name: use-decision-tables-for-combinations
description: When behaviour depends on a combination of conditions like role by state by permission, tabulate every combination so none is missed.
paths: ["tests/**", "src/**"]
severity: should
---
# Use decision tables for combinations

**Rule:** When behaviour depends on a combination of conditions (role × client state × permission), build a decision table listing every combination and its expected outcome, then drive a data provider from it. Authorization combinations test the voter, see security/always-authorize-with-voters.

**Why:** Combinations grow fast and ad-hoc cases quietly skip rows, so a forbidden one slips through. The decision-table technique in `tests/TEST_DESIGN.md` forces every combination onto the page so none is forgotten.

**Good / Bad:**
```php
// Bad: a couple of hand-picked cases, most combos untested.
public function testCoachCanEditOwnClient(): void { /* ... */ }

// Good: one row per combination from the decision table.
public static function access(): array
{
    return [
        ['Coach', 'own',   'edit', true],
        ['Coach', 'other', 'edit', false], // IDOR
        ['Assistant', 'own', 'editPayment', false],
        ['Owner', 'archived', 'restore', true],
    ];
}
```

**Enforced by:** test (data provider covering every row of the decision table) + review.
