.PHONY: all help autophony dev test hooks

all: help

help: ## Show this help.
	@grep "##" $(MAKEFILE_LIST) | grep -v "grep" | sed 's/:.*##\s*/:@\t/g' | column -t -s "@"

autophony: ## Generate a .PHONY rule for your Makefile using all rules in the Makefile(s).
	@grep -oE "^[a-zA-Z-]*\:" $(MAKEFILE_LIST) | sed "s/://g" | xargs echo ".PHONY:"

dev: ## Run Symfony's local server.
	@symfony server:start

test: ## Run PHPUnit tests (usage: make test or make test CMD="--filter testFoo").
	@php bin/phpunit $(CMD)

hooks: ## Install git hooks (points git to .githooks/ and makes them executable).
	@git config core.hooksPath .githooks
	@chmod +x .githooks/*
	@echo "→ Git hooks installed (.githooks/)"
