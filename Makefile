up: docker-up info
down: docker-down

init: docker-down docker-build permission docker-up composer-install migrate info

cp-env:
	cp app/.env.example app/.env

permission:
	sudo chmod 777 -R docker/storage
	sudo chmod 777 -R app/web/assets

docker-up:
	docker-compose up -d

docker-down:
    #очистит все запущеные контейнеры
	docker-compose down --remove-orphans

docker-build:
	docker-compose build

web-bash:
	docker exec -it web bash

migrate:
	docker-compose run --rm webserver php yii migrate/up --interactive=0

composer-install:
	docker-compose run --rm webserver php composer install

info:
	echo "app - http://localhost:8888"
	echo "phpmyadmin - http://localhost:8080"