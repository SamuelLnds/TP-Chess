.PHONY: help install lint lint-fix lint-report quality-report clean

help:
	@echo "Chess Game - Development Tools"
	@echo "================================"
	@echo ""
	@echo "Available commands:"
	@echo "  make install         - Install development dependencies"
	@echo "  make lint            - Run code quality checks (PHPStan + PHP CodeSniffer)"
	@echo "  make lint-fix        - Automatically fix code style issues"
	@echo "  make quality-report  - Generate a full quality report"
	@echo "  make phpstan         - Run only PHPStan analysis"
	@echo "  make phpcs           - Run only PHP CodeSniffer analysis"
	@echo "  make phpcbf          - Fix PHP CodeSniffer violations"
	@echo "  make clean           - Clean up reports and cache"
	@echo "  make help            - Show this help message"
	@echo ""

install:
	composer install --dev

lint:
	@echo "Running PHP Code Quality Checks..."
	@./vendor/bin/phpstan analyse -c tools/quality/phpstan.neon
	@./vendor/bin/phpcs --standard=tools/quality/phpcs.xml

lint-fix:
	@echo "Fixing code style issues..."
	@./vendor/bin/phpcbf --standard=tools/quality/phpcs.xml
	@echo "Done! Review the changes and commit."

quality-report:
	@php tools/quality/generate-report.php

phpstan:
	@echo "Running PHPStan analysis..."
	@./vendor/bin/phpstan analyse -c tools/quality/phpstan.neon

phpcs:
	@echo "Running PHP CodeSniffer..."
	@./vendor/bin/phpcs --standard=tools/quality/phpcs.xml

phpcbf:
	@echo "Fixing PHP CodeSniffer violations..."
	@./vendor/bin/phpcbf --standard=tools/quality/phpcs.xml

clean:
	@echo "Cleaning up..."
	@rm -rf reports/
	@rm -rf .phpstan
	@echo "Done!"

.DEFAULT_GOAL := help
