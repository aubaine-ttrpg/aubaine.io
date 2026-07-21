---
name: distributions-must-normalize
description: Build every probability distribution so its masses provably sum to 1 and assert that exact total before the distribution is used.
paths: ["codex/**/*.py"]
severity: should
---
# Distributions must normalize

**Rule:** A probability distribution's masses **provably sum to 1**. Build the pmf as integer counts over one integer total so the sum is exact, then `assert sum(dist.values()) == 1` before you return it, write it, or feed it into another calculation. Because the masses are `Fraction`, the check is an exact equality (`== 1`), not a tolerance.

**Why:** A pmf whose masses do not total 1 is a bug: a dropped outcome, a double-counted multiplicity, or a wrong denominator. That skew then poisons every mean, standard deviation, and success rate derived from it, with no error to catch downstream. With `Fraction` the sum is exact, so the invariant is a plain equality that fails fast at the source. It is a cheap guard on the exact math the codex depends on (codex/README.md).

**Good / Bad:**
```python
# Bad: return the pmf unchecked. A dropped outcome or wrong total goes unnoticed.
from fractions import Fraction

def pmf(counts: dict[int, int], n: int, faces: int) -> dict[int, Fraction]:
    total = Fraction(faces**n)
    return {s: Fraction(c) / total for s, c in counts.items()}
```
```python
# Good: assert the exact total before the distribution leaves the function.
from fractions import Fraction

def pmf(counts: dict[int, int], n: int, faces: int) -> dict[int, Fraction]:
    total = Fraction(faces**n)
    dist = {s: Fraction(c) / total for s, c in counts.items()}
    assert sum(dist.values()) == 1  # exact: Fraction, so equality holds or the build is wrong
    return dist
```

**See also:** [[compute-probability-exactly-not-by-simulation]], [[no-float-for-probability]], python/prefer-pure-functions-and-plain-data, testing/always-test-critical-paths.

**Enforced by:** pytest (a test sums each distribution's masses and asserts `== 1`) + review; the runtime `assert` fails the lab immediately.
