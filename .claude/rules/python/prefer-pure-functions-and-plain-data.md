---
name: prefer-pure-functions-and-plain-data
description: Write the lab as module-level pure functions over plain data (dict, int, Fraction), keep tuning parameters in UPPERCASE module constants, and avoid classes, dataclasses, and enums.
paths: ["codex/**/*.py"]
severity: should
---
# Prefer pure functions and plain data

**Rule:**

- Write the lab as **module-level functions**. Each is pure: its result depends only on its arguments, it holds no state, and it mutates neither its inputs nor the filesystem.
- Pass and return **plain data**: a `dict`, an `int`, a `Fraction`, a `tuple`, a `list`. Do not wrap a value in a class, dataclass, or enum when a dict keyed by a name (or an int) already carries it.
- Put every tuning parameter (a face count, a sweep range, an output precision) in an **UPPERCASE module constant**, not a literal buried inside a function body.

**Why:** A pure function over plain data is tested by asserting on one exact return value, with no fixtures, no mocks, and no object graph to construct. The balancing lab derives exact numbers, so a function that is a pure function of its arguments is the smallest reproducible unit. State and lifecycle (what a class adds) are cost the math never spends, and an enum or dataclass only re-wraps a value a dict or int already names. PEP 20 (the Zen of Python) prefers simple over complex. A named module constant keeps a tuned value in one place, so changing it is one edit instead of a hunt through call sites.

**Good / Bad:**
```python
# Bad: a class carries state the math never needs, and hides a tuned value in a method.
class Balancer:
    def __init__(self) -> None:
        self.round_places = 6          # tuning value trapped on an instance
        self._cache: dict = {}         # state the pure computation does not need

    def mean(self, dist):              # result depends on self, not just the argument
        ...
```
```python
# Good: an UPPERCASE constant names the parameter once; a pure function returns plain data.
ROUND_PLACES = 6                       # one named home for the output precision

def mean(dist: dict[int, Fraction]) -> float:
    """Expected value of a pmf. Depends only on dist and mutates nothing."""
    return round(float(sum(s * p for s, p in dist.items())), ROUND_PLACES)
```

**See also:** [[use-modern-type-hints]], [[keep-scripts-path-relative-and-package-free]], architecture/separate-math-presentation-generation, architecture/keep-it-simple, architecture/prefer-composition-over-inheritance.

**Enforced by:** ruff (`make lint`) + review.
