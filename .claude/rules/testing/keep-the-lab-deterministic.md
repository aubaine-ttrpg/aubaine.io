---
name: keep-the-lab-deterministic
description: The lab computes by exact enumeration, never by sampling; no random source, no wall-clock, nothing to seed.
paths: ["codex/**"]
severity: must
---
# Keep the lab deterministic

**Rule:** The lab derives its numbers by exact enumeration of the whole outcome space, never by sampling. No `import random`, no `numpy.random`, no wall-clock (`time`, `datetime.now`), no environment-dependent input. There is no seed because there is nothing random to seed: the same inputs always produce byte-identical outputs.

**Why:** Exact enumeration makes every result reproducible and checkable against a hand derivation. Sampling would introduce run-to-run variance, forcing the fuzzy assertions that [[assert-exact-golden-values]] forbids and hiding real drift under the noise. Determinism is also what lets `make regen` reproduce every committed `data/` file byte for byte, which [[guard-generated-data-against-drift]] relies on. No random source and no clock means no seed to manage and no flaky test.

**Good / Bad:**
```python
# Bad: estimate a probability by sampling; the number wobbles run to run and cannot be asserted exactly.
import random
hits = sum(1 for _ in range(1_000_000) if sum(random.randint(1, 12) for _ in range(3)) >= 20)
p = hits / 1_000_000   # about 0.5, never exactly 1/2, and different every run

# Good: enumerate the whole space; the answer is exact and identical on every run.
assert dice.p_at_least(dice.roll_distribution(), 20) == Fraction(1, 2)
```

**See also:** [[assert-exact-golden-values]], [[test-distribution-invariants]], [[guard-generated-data-against-drift]].

**Enforced by:** ruff (flake8-tidy-imports banned-api on `random` and wall-clock modules) + review.
