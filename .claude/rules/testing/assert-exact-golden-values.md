---
name: assert-exact-golden-values
description: Lab tests assert exact Fraction values for derived probabilities and counts, use math.isclose only for genuine floats, and carry one derivation comment per expected constant.
paths: ["codex/**"]
severity: must
---
# Assert exact golden values

**Rule:** In the lab, assert the exact value of every derived probability or count as a `Fraction` with `==`. Reserve `math.isclose` for numbers that are genuinely floating point (a mean, a standard deviation, a success chance at a flat DC). Give every expected constant one comment that states how it is derived, so a reader can check it by hand.

**Why:** The lab computes by exact enumeration, so a probability is a rational number with an exact value. Comparing it as a float throws that exactness away and lets a real drift hide under the tolerance, which is the loosening [[never-weaken-a-failing-test]] forbids. `math.isclose` exists for values that cannot be represented exactly, and PEP 485 defines it for exactly that use. The derivation comment ties each constant to the formula that produced it.

**Good / Bad:**
```python
# Bad: an exact probability compared as a float, the assertion is fuzzy and a real drift can pass.
assert float(dice.crit_success()) == pytest.approx(0.000578)

# Good: exact Fraction with the derivation in a comment; isclose only for a true float.
assert dice.crit_success() == Fraction(1, 1728)   # a twelve on each of 3 dice: (1/12) ** 3
assert math.isclose(dice.distribution_stdev(base), 5.979, abs_tol=0.001)  # stdev is a float
```

**See also:** [[test-distribution-invariants]], [[keep-the-lab-deterministic]], [[never-weaken-a-failing-test]].

**Enforced by:** pytest (exact `==` on `Fraction`, `math.isclose` on floats) + review.
