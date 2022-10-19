.PHONY: analyse
analyse: ## Run phpstan analyser
	./vendor/bin/phpstan analyse --memory-limit 1G

.PHONY: help
help:  ## Display help
	@awk -F ':|##' '/^[^\t].+?:.*?##/ {printf "\033[36m%-30s\033[0m %s\n", $$1, $$NF}' $(MAKEFILE_LIST) | sort

.PHONY: update
update: ## Update Composer
	valet composer update
