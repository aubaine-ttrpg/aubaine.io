---
name: depend-on-data-not-code-across-domains
description: A codex domain reads another domain's committed data JSON as its input and never imports that domain's Python module.
paths: ["codex/**/*.py"]
severity: should
---
# Depend on data, not code, across domains

**Rule:** When one codex domain needs a result another domain produces, it reads that domain's committed `data/*.json`. It does not import the other domain's lab module, does not add the other lab to `sys.path`, and does not call the other domain's functions. Cross-domain coupling goes through the data of record, never through code. The combat-system lab reads `dice-and-modifiers/data/roll-distributions.json`; it never imports `dice`.

**Why:** The committed JSON is the source of record for a domain's numbers, and it is deterministic. A dependent domain that reads it is pinned to reviewed, versioned output, not to another lab's private functions that can be renamed or reshaped at any time. Importing across domains couples internals, creates import-path and cycle problems between labs, and means regenerating one domain can silently change another's results without going through the reviewed data file. Reading the data keeps each lab self-contained and each number owned in one place.

**Good / Bad:**
```python
# Bad: combat reaches into the dice lab's Python, coupling to its internals.
import sys
sys.path.insert(0, "../dice-and-modifiers/lab")
import dice
cdf = dice.cdf_at_least(dice.roll_distribution())  # cross-domain code import

# Good: combat reads the committed data file the dice domain regenerates.
ROLL_DISTRIBUTIONS = (
    Path(__file__).resolve().parents[2] / "dice-and-modifiers" / "data" / "roll-distributions.json"
)
data = json.loads(ROLL_DISTRIBUTIONS.read_text(encoding="utf-8"))
cdf = {int(k): v for k, v in data["0"]["cdf_at_least"].items()}
```

**See also:** [[separate-math-presentation-generation]], process/own-canonical-sources-of-truth, process/never-duplicate-reference-living-files.

**Enforced by:** ruff + review (no cross-domain lab imports; a domain's inputs come from committed `data/`).
