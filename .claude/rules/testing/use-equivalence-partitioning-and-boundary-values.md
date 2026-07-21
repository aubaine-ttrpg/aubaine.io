---
name: use-equivalence-partitioning-and-boundary-values
description: Test one input per equivalence class plus the boundaries empty, one, max, and max+1, not many near-identical valid cases.
severity: should
---
# Use equivalence partitioning and boundary values

**Rule:** Group inputs that behave identically and test one representative per class, not fifty. Always add the boundaries empty, one, max, and max+1 (empty book, one page, the layout page limit, one page past it). Feed them through a PHPUnit data provider or a pytest `@pytest.mark.parametrize`.

**Why:** Identical-behaviour inputs add runtime, not coverage, and bugs cluster at edges. Equivalence partitioning and boundary values are the technique that finds the edge-case category of the five, see [[always-cover-the-five-categories]].

**Good / Bad:**
```php
// Bad: 50 near-identical valid page titles, no boundaries.
public static function titles(): array { return [['Combat'], ['Stealth'], /* ...48 more */]; }

// Good: one per class + boundaries.
public static function books(): array
{
    return [['empty' => 0], ['one' => 1], ['max' => 64], ['over' => 65]];
}
```
```python
# Good: one representative per class plus the boundary, via parametrize.
@pytest.mark.parametrize("n_dice", [3, 4, 7])  # minimum, one extra, several extra
def test_keep_sum_normalises(n_dice):
    assert sum(dice.keep_sum_distribution(n_dice, 3, "best").values()) == 1

def test_keep_below_minimum_is_rejected():
    with pytest.raises(ValueError):
        dice.keep_sum_distribution(2, 3, "best")  # the other boundary: too few dice to keep
```

**See also:** [[always-cover-the-five-categories]], [[use-decision-tables-for-combinations]].

**Enforced by:** review (data providers and parametrize cover one-per-class plus empty, one, max, max+1).
