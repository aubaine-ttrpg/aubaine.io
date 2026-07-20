# ─────────────────────────────────────────────────────────────
# Sigil: the shared design system + Storybook
# ─────────────────────────────────────────────────────────────
.PHONY: help install dev storybook build

help: ## Show this help.
	@grep "##" $(MAKEFILE_LIST) | grep -v "grep" | sed 's/:.*##\s*/:@\t/g' | column -t -s "@"

install: ## Install Storybook and preview tooling.
	@if [ -f package.json ]; then npm install; else echo "sigil: nothing to install yet"; fi

dev: storybook ## Alias for storybook.

storybook: ## Run Storybook (http://localhost:6006).
	@npm run storybook

build: ## Build the static Storybook site into storybook-static/.
	@npm run build-storybook
