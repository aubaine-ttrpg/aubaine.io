---
name: use-modern-type-hints
description: Open every module with from __future__ import annotations, write builtin generics and pipe unions, annotate every function with a return type, and give each a docstring that names the computed quantity and its invariant.
paths: ["codex/**/*.py"]
severity: must
---
# Use modern type hints

**Rule:**

- Every module opens with `from __future__ import annotations` as its first import, before any other.
- Write generics with builtin types and pipe unions: `dict[int, Fraction]`, `tuple[int, ...]`, `list[dict]`, `int | None`. Never import `Dict`, `List`, `Optional`, or `Union` from `typing`.
- Annotate every function: a type on each parameter and a return type, including `-> None` for a function that returns nothing.
- Give each function a docstring that names the quantity it computes and states its invariant, the property that always holds of the return value (a pmf whose values sum to 1, counts that sum to `faces ** n`, a cdf that is monotone).

**Why:** The lab targets Python 3.14 (`requires-python = ">=3.14"` in `codex/pyproject.toml`), so every modern form is available. `from __future__ import annotations` (PEP 563) makes annotations lazy strings, so a forward reference costs nothing and the type text never runs at import. Builtin generics (PEP 585) and the pipe union (PEP 604) drop the `typing` shim, so the signature reads as the plain data it describes. A return type on every function (PEP 484) makes the contract explicit at the boundary the type checker guards. The lab derives exact numbers, so a docstring that states the invariant (PEP 257) is exactly what a golden test pins: it tells the reader what the return value always satisfies, not how it is computed.

**Good / Bad:**
```python
# Bad: legacy typing imports, an unannotated return, a docstring that omits the invariant.
from typing import Dict, Optional

def keep_sum_distribution(n_dice, keep, mode="best") -> Dict[int, "Fraction"]:
    """Roll some dice."""            # says nothing the caller can rely on
    ...

def one_in(x: float):                # missing return type
    ...
```
```python
# Good: future annotations, builtin generics and pipe unions, full signatures, stated invariants.
from __future__ import annotations

from fractions import Fraction

def keep_sum_distribution(
    n_dice: int, keep: int, mode: str = "best"
) -> dict[int, Fraction]:
    """Exact pmf of the kept sum; its values sum to 1 and its keys are the attainable sums."""
    ...

def one_in(x: float) -> str | None:
    """Odds phrased as '1 in N', or None when x is 0 (the event never occurs)."""
    ...
```

**See also:** [[prefer-pure-functions-and-plain-data]], [[ruff-is-the-one-linter-and-formatter]], architecture/separate-math-presentation-generation.

**Enforced by:** ruff (`make lint`) + review.
