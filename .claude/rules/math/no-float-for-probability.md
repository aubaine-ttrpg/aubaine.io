---
name: no-float-for-probability
description: Represent every probability or exact rate as a fractions.Fraction or integer counts so nothing drifts, and use float only for a final rendered percentage.
paths: ["codex/**/*.py"]
severity: must
---
# No float for probability

**Rule:** Never use `float` for a probability, an exact rate, or any value that has to add up or be compared exactly. Carry the value as a `fractions.Fraction` (or integer counts) through all of the math, so nothing drifts. Convert to `float` only at the end, in the presentation layer, to render a percentage or a "1 in N" label. A stored, summed, or asserted probability is a `Fraction`; a `float` probability is a display artifact and is never fed back into another computation.

**Why:** IEEE 754 binary floating point cannot represent most decimal fractions exactly, so `0.1 + 0.2` drifts and summed masses stop equalling 1. A `float` probability also fails an exact equality assertion and its last digits vary between machines, which breaks byte-stable generation. `Fraction` keeps every ratio exact, so distributions normalize, tests assert `==`, and regeneration is deterministic. This is the same discipline the app applies to money (money is integer minor units, never float): keep the exact value exact and let `float` appear only in the rendered figure.

**Good / Bad:**
```python
# Bad: probabilities as float. They drift and the total is not exactly 1.
p_edge = 0.1 + 0.2                 # 0.30000000000000004, not 0.3
dist = {1: 0.5, 2: 0.5}
assert sum(dist.values()) == 1     # brittle: rounding can make this fail
```
```python
# Good: Fraction through the math; float only for the rendered percent.
from fractions import Fraction

dist = {1: Fraction(1, 2), 2: Fraction(1, 2)}
assert sum(dist.values()) == 1                 # exact, always holds
label = f"{float(dist[1]) * 100:.1f}%"         # float appears only here, for display
```

**See also:** [[compute-probability-exactly-not-by-simulation]], [[distributions-must-normalize]], [[make-generation-reproducible]], php/never-use-float-for-exact-quantities.

**Enforced by:** ruff (`make lint`) + review (a `float` in the math path, or a `float` fed back into a computation, is rejected); pytest asserts exact `Fraction` results.
