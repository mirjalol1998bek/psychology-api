DC := docker compose
PHP := $(DC) exec php

.DEFAULT_GOAL := help

help: ## Buyruqlar ro'yxati
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-16s\033[0m %s\n", $$1, $$2}'

up: ## Konteynerlarni ishga tushirish (+build)
	$(DC) up -d --build php nginx db

down: ## Konteynerlarni to'xtatish
	$(DC) down

restart: down up ## Qayta ishga tushirish

install: up ## To'liq o'rnatish (DB kutadi, migratsiya, JWT kalitlar)
	$(PHP) composer install
	$(PHP) bin/console ask:install

sh: ## php konteyneriga kirish
	$(PHP) bash

console: ## bin/console (masalan: make console c="cache:clear")
	$(PHP) bin/console $(c)

migrate: ## Migratsiyalarni qo'llash
	$(PHP) bin/console doctrine:migrations:migrate --no-interaction

migration: ## Yangi migratsiya generatsiya qilish
	$(PHP) bin/console make:migration

cc: ## Keshni tozalash
	$(PHP) bin/console cache:clear

logs: ## php konteyneri loglari
	$(DC) logs -f php

cs: ## PHP-CS-Fixer (keyin qo'shiladi)
	$(PHP) vendor/bin/php-cs-fixer fix

stan: ## PHPStan (keyin qo'shiladi)
	$(PHP) vendor/bin/phpstan analyse

test: ## PHPUnit (keyin qo'shiladi)
	$(PHP) bin/phpunit

.PHONY: help up down restart install sh console migrate migration cc logs cs stan test
