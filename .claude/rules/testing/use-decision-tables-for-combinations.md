---
name: use-decision-tables-for-combinations
description: When behaviour depends on a combination of conditions, tabulate every combination so none is missed.
severity: should
---
# Use decision tables for combinations

**Rule:** When behaviour depends on a combination of conditions (net advantage sign x keep mode x outcome; book state x export format), build a decision table listing every combination and its expected outcome, then drive a PHPUnit data provider or a pytest `parametrize` from it.

**Why:** Combinations grow fast and ad-hoc cases quietly skip rows, so a wrong one slips through. A decision table forces every combination onto the page so none is forgotten.

**Good / Bad:**
```php
// Bad: a couple of hand-picked cases, most combinations untested.
public function testBookWithPagesExports(): void { /* ... */ }

// Good: one row per combination from the decision table.
public static function exports(): array
{
    return [
        ['pages' => true,  'skillTree' => true,  'format' => 'pdf',  'ok' => true],
        ['pages' => true,  'skillTree' => false, 'format' => 'pdf',  'ok' => true],
        ['pages' => false, 'skillTree' => true,  'format' => 'pdf',  'ok' => false], // no pages
        ['pages' => true,  'skillTree' => true,  'format' => 'json', 'ok' => true],
    ];
}
```
```python
# Good: one row per combination, driven from parametrize.
@pytest.mark.parametrize("av,dv,expected", [
    (0, 0, Fraction(1, 1728)),    # no edge: 3 maxima among 3 dice
    (1, 0, Fraction(5, 2304)),    # advantage: at least 3 maxima among 4 dice
    (0, 1, Fraction(1, 20736)),   # disadvantage: every one of 4 dice is a maximum
])
def test_crit_success_by_edge(av, dv, expected):
    assert dice.crit_success(av, dv) == expected
```

**See also:** [[use-equivalence-partitioning-and-boundary-values]], [[always-cover-the-five-categories]].

**Enforced by:** PHPUnit data providers and pytest parametrize (one case per row) + review.
