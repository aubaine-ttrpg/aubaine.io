# ─────────────────────────────────────────────────────────────
# Catalyst (Symfony authoring tool — dev-only, local)
# ─────────────────────────────────────────────────────────────

.PHONY: help install dev watch build test stan cs fix

help: ## Show this help.
	@grep "##" $(MAKEFILE_LIST) | grep -v "grep" | sed 's/:.*##\s*/:@\t/g' | column -t -s "@"

install: ## Install PHP + JS dependencies and build dev assets.
	@[ -f composer.json ] && composer install || echo "catalyst: placeholder (no composer.json yet)"
	@[ -f package.json ] && npm install && npm run dev || true

dev: ## Run the Symfony CLI dev server (http://127.0.0.1:8000).
	@symfony serve --no-tls

watch: ## Rebuild assets on change.
	@npm run watch

build: ## Build optimized assets.
	@npm run build

test: ## Run the PHPUnit suite.
	@vendor/bin/phpunit

stan: ## Static analysis (PHPStan).
	@vendor/bin/phpstan analyse

cs: ## Check code style (php-cs-fixer, dry run).
	@vendor/bin/php-cs-fixer fix --dry-run --diff

fix: ## Apply code-style fixes.
	@vendor/bin/php-cs-fixer fix
