---
name: test-distribution-invariants
description: Lab tests pin distribution invariants, probabilities sum to 1 and known symmetries hold, not only individual point values.
paths: ["codex/**"]
severity: should
---
# Test distribution invariants

**Rule:** For every distribution the lab produces, test its invariants, not just individual point probabilities. Assert normalization (the masses sum to 1) and every known symmetry (disadvantage mirrors advantage: crit-fail under disadvantage equals crit-success under advantage). Run the normalization check across the range of inputs, not one case.

**Why:** A single point value can be right while the whole distribution is malformed, masses that do not sum to 1, or a broken mirror between advantage and disadvantage. Normalization is the probability axiom that total mass is 1, so a sum that misses proves a dropped or double-counted outcome that no point check would catch. A symmetry test proves the model treats the two edges as true mirrors. These invariants find a class of errors the exact point values in [[assert-exact-golden-values]] cannot.

**Good / Bad:**
```python
# Bad: only point probabilities checked; a distribution that does not sum to 1 still passes.
assert dice.p_at_least(base, 20) == Fraction(1, 2)

# Good: pin the invariants as well.
assert sum(base.values()) == 1                          # normalisation: it is a probability distribution
for n in range(3, 8):
    assert sum(dice.keep_sum_distribution(n, 3, "best").values()) == 1
assert dice.crit_fail(dv=1) == dice.crit_success(av=1)  # symmetry: disadvantage mirrors advantage
```

**See also:** [[assert-exact-golden-values]], [[keep-the-lab-deterministic]], [[use-equivalence-partitioning-and-boundary-values]].

**Enforced by:** pytest (normalization and symmetry assertions) + review.
