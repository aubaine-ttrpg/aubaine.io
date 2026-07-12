# ─────────────────────────────────────────────────────────────
# Catalyst (Symfony authoring tool)
# ─────────────────────────────────────────────────────────────

.PHONY: help install export sync

help: ## Show this help.
	@grep "##" $(MAKEFILE_LIST) | grep -v "grep" | sed 's/:.*##\s*/:@\t/g' | column -t -s "@"

install: ## Install PHP dependencies.
	@[ -f composer.json ] && composer install || echo "catalyst: placeholder (no composer.json yet)"

export: ## Export the local db into ../content/ (commit the result).
	@php bin/console catalyst:export

sync: ## Read ../content/ and update the local db.
	@php bin/console catalyst:sync
