.PHONY: all help md autophony dev clear migrations migrate drop-database

all: help

# - Misc.
help: ## Show this help.
	@grep "##" $(MAKEFILE_LIST) | grep -v "grep" | sed 's/:.*##\s*/:@\t/g' | column -t -s "@" 

md: ## Show this help but in a markdown styled way. This can be used when updating the Makefile to generate documentation and simplify README.md's 'Make rules' section update.
	@grep "##" $(MAKEFILE_LIST) | grep -v "grep" | sed -E 's/([^:]*):.*##\s*/- ***\1***:@\t/g' | column -t -s "@"

autophony: ## Generate a .PHONY rule for your Makefile using all rules in the Makefile(s).
	@grep -oE "^[a-zA-Z-]*\:" $(MAKEFILE_LIST) | sed "s/://g" | xargs echo ".PHONY:"

# - Simple workflow
dev: ## Run Symfony's local server.
	@symfony local:server:start --no-tls

clear: ## Clear service's cache. Equivalent to 'cache:clear' using php console.
	@php bin/console cache:clear

migrations: ## Make Migrations. Equivalent to 'make:migration' using php console.
	@php bin/console make:migration -n --formatted

migrate: ## Apply Migrations. Equivalent to 'doctrine:migrations:migrate' using php console.
	@php bin/console doctrine:migrations:migrate -n

# - Detailed workflow
drop-database: ## Drop Database using `doctrine:schema:drop`.
	@php bin/console doctrine:schema:drop --full-database --force