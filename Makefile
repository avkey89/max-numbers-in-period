init: docker-down-clear docker-pull docker-build docker-up install
init-db: db-create db-migrate db-fixtures
up: docker-up
down: docker-down
restart: down up

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull --include-deps

docker-build:
	DOCKER_BUILDKIT=1 COMPOSE_DOCKER_CLI_BUILD=1 docker-compose build --build-arg BUILDKIT_INLINE_CACHE=1

install:
	docker-compose exec -T php-fpm composer install --no-interaction --ansi --no-suggest

db-create:
	docker-compose exec -T php-fpm php bin/console doctrine:database:create

db-migrate:
	docker-compose exec -T php-fpm php bin/console doctrine:migrations:migrate -n

db-fixtures:
	docker-compose exec -T php-fpm php bin/console doctrine:fixtures:load -n

test:
	docker-compose exec -T php-fpm php vendor/phpunit/phpunit/phpunit
