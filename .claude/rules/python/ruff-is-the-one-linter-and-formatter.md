---
name: ruff-is-the-one-linter-and-formatter
description: Use ruff as the only linter and formatter for the codex lab, take the line length of 100 from pyproject.toml, and never hand-format or add a second tool.
paths: ["codex/**/*.py"]
severity: must
---
# Ruff is the one linter and formatter

**Rule:**

- `ruff check` is the only linter and `ruff format` is the only formatter for the codex lab. Do not add black, isort, flake8, autopep8, yapf, or pylint, and do not run any of them by hand.
- The line length is 100, set once in `codex/pyproject.toml` under `[tool.ruff]`. Do not restate it in another file, override it per module, or hand-wrap to a different width.
- Do not hand-format. Let `ruff format` lay out the code and commit its output; never fight it with manual spacing or `# fmt` comments.
- Run the linter through the Makefile: `make lint` runs `ruff check .`. Run `ruff format` before you commit so the working tree already matches the formatter.

**Why:** Ruff replaces the linter-plus-formatter-plus-import-sorter stack with one deterministic pass, so the same input always yields the same layout and a diff shows content, not whitespace. Keeping the line length in one `[tool.ruff]` block makes it the single source: any other copy would drift the moment the value changes, which the no-drift rule forbids. Ruff formatting follows the PEP 8 style baseline, so contributors do not argue layout in review; the tool decides and review spends its attention on the math.

**Good / Bad:**
```text
# Bad: a second formatter, and the line length copied into a per-tool config that will drift.
$ pip install black isort
$ black codex/ && isort codex/       # two more tools, a different idea of correct
# setup.cfg
[flake8]
max-line-length = 88                 # a second home for a value pyproject.toml already owns
```
```text
# Good: one tool, one config, run through the Makefile.
# codex/pyproject.toml
[tool.ruff]
line-length = 100                    # the single source for width

$ make lint                          # runs: ruff check .
$ ruff format                        # the only formatter; commit its output
```

**See also:** [[use-modern-type-hints]], [[prefer-pure-functions-and-plain-data]], process/own-canonical-sources-of-truth.

**Enforced by:** ruff itself (`make lint` runs `ruff check .`, and `ruff format --check` verifies the layout) + review.
