---
name: use-equivalence-partitioning-and-boundary-values
description: Test one input per equivalence class and always the boundaries empty, one, max and max+1, not fifty near-identical valid cases.
paths: ["tests/**", "src/**"]
severity: should
---
# Use equivalence partitioning and boundary values

**Rule:** Group inputs that behave identically and test one representative per class, not fifty. Always add the boundaries empty, one, max and max+1 (empty list, 1 row, the pagination limit, one past it; 0 €, 1 cent, max amount). Feed them through a PHPUnit data provider.

**Why:** Identical-behaviour inputs add runtime, not coverage, and bugs cluster at edges. These are the equivalence-partitioning and boundary-value techniques in `tests/TEST_DESIGN.md`, and the boundaries are the edge-case category of the five.

**Good / Bad:**
```php
// Bad: 50 near-identical valid emails, no boundaries.
public static function emails(): array { return [['a@x.io'], ['b@x.io'], /* ...48 more */]; }

// Good: one per class + boundaries.
public static function lists(): array
{
    return [['empty' => 0], ['one' => 1], ['max' => 50], ['over' => 51]];
}
```

**Enforced by:** review (data providers cover one-per-class plus empty/one/max/max+1).
