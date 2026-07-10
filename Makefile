# ─────────────────────────────────────────────────────────────
# Variables
# ─────────────────────────────────────────────────────────────
# - Apps
CATALYST_DIR = apps/catalyst
ALMANACH_DIR = apps/almanach
# - Shared (committed data)
CODEX_DIR    = codex
CONTENT_DIR  = content

.PHONY: all help md autophony install export sync dev build build-site preview

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
# Pipeline
# ─────────────────────────────────────────────────────────────
install: ## Install dependencies for both apps.
	@composer --working-dir=$(CATALYST_DIR) install
	@npm --prefix $(ALMANACH_DIR) install

export: ## [Catalyst] Export the local db into content/ (commit the result).
	@php $(CATALYST_DIR)/bin/console catalyst:export

sync: ## [Catalyst] Read content/ and update the local db.
	@php $(CATALYST_DIR)/bin/console catalyst:sync

dev: ## [Almanach] Run the Astro dev server.
	@npm --prefix $(ALMANACH_DIR) run dev

build: export ## [Almanach] Export from Catalyst, then build the static site.
	@npm --prefix $(ALMANACH_DIR) run build

build-site: ## [Almanach] Build the static site from committed content/ (CI, no Catalyst).
	@npm --prefix $(ALMANACH_DIR) run build

preview: ## [Almanach] Preview the built static site locally.
	@npm --prefix $(ALMANACH_DIR) run preview
