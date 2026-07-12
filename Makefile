# ─────────────────────────────────────────────────────────────
# Root orchestrator
# ─────────────────────────────────────────────────────────────
# Delegates to each project's own Makefile and owns only the
# cross-project pipeline. Stack-specific targets live in
# codex/, catalyst/, and almanach/.

.PHONY: all help md autophony install dev build build-site preview test lint

all: help

# ─────────────────────────────────────────────────────────────
# Meta
# ─────────────────────────────────────────────────────────────
help: ## Show this help.
	@grep "##" $(MAKEFILE_LIST) | grep -v "grep" | sed 's/:.*##\s*/:@\t/g' | column -t -s "@"

md: ## Show this help in a markdown-styled way (for the README 'Make rules' section).
	@grep "##" $(MAKEFILE_LIST) | grep -v "grep" | sed -E 's/([^:]*):.*##\s*/- ***\1***:@\t/g' | column -t -s "@"

autophony: ## Regenerate the .PHONY line from every rule in the Makefile.
	@grep -oE "^[a-zA-Z-]*\:" $(MAKEFILE_LIST) | sed "s/://g" | xargs echo ".PHONY:"

# ─────────────────────────────────────────────────────────────
# Fan-out
# ─────────────────────────────────────────────────────────────
install: ## Install every project's dependencies.
	@$(MAKE) -C codex install
	@$(MAKE) -C catalyst install
	@$(MAKE) -C almanach install

dev: ## Run the local dev server(s). Catalyst now; Almanach will join later.
	@$(MAKE) -C catalyst dev

test: ## Run the codex balancing-lab tests.
	@$(MAKE) -C codex test

lint: ## Lint the codex balancing lab.
	@$(MAKE) -C codex lint

# ─────────────────────────────────────────────────────────────
# Cross-project pipeline
# ─────────────────────────────────────────────────────────────
build: ## Export from Catalyst, then build the Almanach static site.
	@$(MAKE) -C catalyst export
	@$(MAKE) -C almanach build

build-site: ## Build the static site from committed content/ (CI, no Catalyst).
	@$(MAKE) -C almanach build-site

preview: ## Preview the built static site locally.
	@$(MAKE) -C almanach preview
