include .env

init: init-network build up

init-dev: init-network build-dev up dev

dev:
	@docker compose -f docker-compose.dev.yml up -d

up:
	@docker compose up -d

down:
	@docker compose -f docker-compose.dev.yml -f docker-compose.yml down -v

in-php:	
	@docker exec -it ${APP_NAME}.yii bash

in-mysql:
	@docker exec -it ${APP_NAME}.db mysql -u${DB_USERNAME} -p${DB_PASSWORD}

fill-dump:
	@docker exec -i ${APP_NAME}.db mysql -uroot -p${DB_ROOT_PASSWORD} ${DB_DATABASE} < ./docker/mysql/dump.sql

build:
	@docker compose build --no-cache

build-dev:
	@docker compose -f docker-compose.dev.yml -f docker-compose.yml build --no-cache

init-network:
	@docker network create -d bridge pkmm

load-fixtures:
	@docker exec -it ${APP_NAME}.yii php yii fixture/load '*'

start-tests:
	@docker exec ${APP_NAME}.yii vendor/bin/codecept run
