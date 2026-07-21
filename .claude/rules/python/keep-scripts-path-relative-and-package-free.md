---
name: keep-scripts-path-relative-and-package-free
description: Resolve every path from the running script's own location and keep the lab a set of runnable scripts rather than an installable package.
paths: ["codex/**/*.py"]
severity: should
---
# Keep scripts path-relative and package-free

**Rule:**

- Resolve every file path from the running script's own location. Start from `Path(__file__).resolve()`, then walk with `.parent` or `.parents[n]` to reach the target (a domain's `data/` sits beside the lab). Never rely on the current working directory, and never hardcode an absolute path.
- The lab is deliberately not an installable package. `codex/pyproject.toml` ships no modules (`py-modules = []`); pytest imports lab modules through `[tool.pytest.ini_options] pythonpath`, and generation scripts are run by path (`python dice-and-modifiers/lab/generate.py`).
- A script must run from any directory and write the same output. Do not add `__init__.py`, a `setup.py` module list, or a `sys.path` edit to turn the lab into an importable library.

**Why:** A path resolved from `__file__` is stable no matter where the process starts, so a generation script run from the repo root, from the domain folder, or in CI writes to the same `data/` every time, and the regenerate-and-compare test stays deterministic. A path resolved from the current working directory breaks the moment someone runs the script from elsewhere. Staying package-free keeps the lab what it is meant to be, a set of runnable scripts that capture the balancing method, not a distributable library; pytest's `pythonpath` already gives the tests their imports without an install step, so nothing is gained by packaging and one more build surface is avoided.

**Good / Bad:**
```python
# Bad: paths hang off the current working directory, so output depends on where you ran it.
from pathlib import Path

DATA = Path("data")                       # only correct when cwd is the lab folder
DATA.mkdir(exist_ok=True)                 # writes into whatever directory you happened to be in
```
```python
# Good: paths resolve from the script itself, so it runs the same from anywhere.
from pathlib import Path

DATA = Path(__file__).resolve().parent.parent / "data"   # the domain's data/, always
```

**See also:** [[prefer-pure-functions-and-plain-data]], [[use-modern-type-hints]], architecture/separate-math-presentation-generation.

**Enforced by:** ruff (`make lint`) + review; the regenerate-and-compare pytest fails if a script cannot find its `data/` from `__file__`.
