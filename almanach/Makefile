# ─────────────────────────────────────────────────────────────
# Almanach (Astro static site)
# ─────────────────────────────────────────────────────────────

.PHONY: help install dev build build-site preview

help: ## Show this help.
	@grep "##" $(MAKEFILE_LIST) | grep -v "grep" | sed 's/:.*##\s*/:@\t/g' | column -t -s "@"

install: ## Install Node dependencies.
	@[ -f package.json ] && npm install || echo "almanach: placeholder (no package.json yet)"

dev: ## Run the Astro dev server.
	@npm run dev

build: ## Build the static site.
	@npm run build

build-site: ## Build the static site from committed content/ (CI, no Catalyst).
	@npm run build

preview: ## Preview the built static site locally.
	@npm run preview
