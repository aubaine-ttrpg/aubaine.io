---
name: separate-math-presentation-generation
description: Keep the codex lab in three layers so pure math never formats strings or writes files, a presentation layer formats, and a generation layer writes the committed data files.
paths: ["codex/**/*.py"]
severity: should
---
# Separate math, presentation, and generation

**Rule:** A codex domain's lab has three layers, each with one job.

- **Math** returns exact values (a `Fraction`, a number, a `dict`). It never formats a string, rounds for display, prints, or touches the filesystem. A math function is a pure function of its arguments.
- **Presentation** turns values into display strings: percentages, "1 in N", signed numbers, a Markdown table. It formats; it does not compute the underlying value and does not write files.
- **Generation** orchestrates: it calls the math, passes the results through presentation, and writes the JSON and Markdown into the domain's `data/` directory. Writing lives here and nowhere else.

Keep these in separate modules (the dice-and-modifiers lab uses `dice.py`, `tables.py`, `generate.py`).

**Why:** A pure math function is tested by asserting on its exact return value, with no files and no string parsing. Mixing a `write_text` or an f-string into the math couples the value to one output format and makes the value untestable on its own. Splitting generation out makes the committed `data/` files a deterministic function of the code, so a regenerate-and-compare test catches any drift between the source and the committed output.

**Good / Bad:**
```python
# Bad: the math function formats and writes, so its value cannot be reused or unit-tested.
def p_success(target: int) -> None:
    p = _compute(target)
    Path("data/success.md").write_text(f"{float(p) * 100:.1f}%")

# Good: three layers, each with one job.
def p_success(target: int) -> Fraction:        # math: exact value, no I/O
    return _compute(target)

def pct(x: float, places: int = 1) -> str:      # presentation: value -> string
    return f"{x * 100:.{places}f}%"

def main() -> None:                             # generation: orchestrate + write
    write_json(DATA / "success.json", {"p": round(float(p_success(20)), 6)})
```

**See also:** [[depend-on-data-not-code-across-domains]], [[keep-it-simple]], process/own-canonical-sources-of-truth.

**Enforced by:** ruff + review; a regenerate-and-compare pytest guards that the generation layer's output matches the committed `data/` files.
