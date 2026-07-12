# ─────────────────────────────────────────────────────────────
# Codex (mechanics + balancing lab)
# ─────────────────────────────────────────────────────────────

VENV := .venv
PY   := $(VENV)/bin/python
PIP  := $(VENV)/bin/pip

.PHONY: help install test lint regen

help: ## Show this help.
	@grep "##" $(MAKEFILE_LIST) | grep -v "grep" | sed 's/:.*##\s*/:@\t/g' | column -t -s "@"

install: ## Create .venv and install the lab (dev tools; add .[plots] for figures).
	@python3 -m venv $(VENV)
	@$(PIP) install --quiet --upgrade pip
	@$(PIP) install --quiet -e ".[dev]"

test: ## Run the balancing-lab tests.
	@$(PY) -m pytest -q

lint: ## Lint the balancing lab.
	@$(VENV)/bin/ruff check .

regen: ## Regenerate every domain's data/ tables.
	@$(PY) dice-and-modifiers/lab/generate.py
	@$(PY) combat-system/lab/generate.py
