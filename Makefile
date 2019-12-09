SYMFONY			= symfony
SYMFONY_CONSOLE	= $(SYMFONY) console
COMPOSER		= $(SYMFONY) composer

##
## Project
## -------
##

build:
	-$(SYMFONY) pecl install xdebug

clean: ## Remove generated files
clean:
	rm -rf vendor

.PHONY: clean

##
## Utils
## -----
##

composer.lock: composer.json
	$(COMPOSER) update --lock --no-scripts --no-interaction

vendor: composer.lock
	$(COMPOSER) install

update:
	$(COMPOSER) update

.PHONY: vendor

##
## Tests
## -----
##

test: ## Run unit and functional tests
test: vendor
	${SYMFONY} php ./vendor/bin/simple-phpunit

test-coverage: vendor
	${SYMFONY} php ./vendor/bin/simple-phpunit --coverage-html var/coverage

.PHONY: test test-coverage

##
## Quality assurance
## -----------------
##

QA	= docker run --rm -v `pwd`:/project mykiwi/phaudit:7.3
ARTEFACTS = var/artefacts

lint: ## Lints yaml files
lint: ly

ly: vendor
	$(SYMFONY_CONSOLE) lint:yaml config

security: ## Check security of your dependencies (https://security.symfony.com/)
security:
	$(QA) security-checker security:check composer.lock

phploc: ## PHPLoc (https://github.com/sebastianbergmann/phploc)
	$(QA) phploc --exclude=vendor ./

phpmd: ## PHP Mess Detector (https://phpmd.org)
	$(QA) phpmd ./ text .phpmd.xml --exclude vendor

php_codesnifer: ## PHP_CodeSnifer (https://github.com/squizlabs/PHP_CodeSniffer)
	$(QA) phpcs -v --standard=.phpcs.xml --ignore=vendor/ ./

phpcpd: ## PHP Copy/Paste Detector (https://github.com/sebastianbergmann/phpcpd)
	$(QA) phpcpd --exclude=vendor/ ./

phpstan: ## PHP stan (https://github.com/phpstan/phpstan)
	$(QA) phpstan analyse --memory-limit=4G -l 4 -c phpstan.neon DependencyInjection EventSubscriber Traits

phpmetrics: ## PhpMetrics (http://www.phpmetrics.org)
phpmetrics: artefacts
	$(QA) phpmetrics --exclude=vendor --report-html=$(ARTEFACTS)/phpmetrics ./

php-cs-fixer: ## php-cs-fixer (http://cs.sensiolabs.org)
	$(QA) php-cs-fixer fix --dry-run --using-cache=no --verbose --diff

apply-php-cs-fixer: ## apply php-cs-fixer fixes
	$(QA) php-cs-fixer fix --using-cache=no --verbose --diff

artefacts:
	mkdir -p $(ARTEFACTS)

pre-commit: phpmd phpcpd apply-php-cs-fixer phpstan

.PHONY: lint lt ly phploc pdepend phpmd php_codesnifer phpcpd phpmetrics php-cs-fixer apply-php-cs-fixer artefacts phpstan
.DEFAULT_GOAL := help
help:
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
.PHONY: help
