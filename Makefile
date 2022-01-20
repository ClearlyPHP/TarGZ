# This simply runs the tests

PHPUNIT:=$(shell pwd)/vendor/bin/phpunit

.PHONY: tests
tests: $(PHPUNIT)
	@$(PHPUNIT) --coverage-text

html: $(PHPUNIT)
	@$(PHPUNIT) --coverage-html=testoutput
	@echo "HTML Code Coverage Output is in $(shell pwd)/testoutput"

$(PHPUNIT):
	composer install

