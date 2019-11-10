up: docker-up info
down: docker-down

init: docker-down create-dir-file docker-build permission docker-up composer-install migrate info

create-dir-file:
	mkdir -p docker/storage
	sudo mkdir -p app/runtime/cache
	cp app/config/params-local.dist app/config/params-local.php
	cp app/config/db.dist app/config/db.php

permission:
	sudo chmod 777 -R docker/storage
	sudo chmod 777 -R app/web/assets
	sudo chmod 777 -R app/runtime

docker-up:
	docker-compose up -d

docker-down:
    #очистит все запущеные контейнеры
	docker-compose down --remove-orphans

docker-build:
	docker-compose build

web-bash:
	docker exec -it baza_web bash

migrate:
	docker-compose run --rm baza_webserver php yii migrate/up --interactive=0

composer-install:
	sudo docker-compose run --rm baza_webserver composer install

info:
	echo "app - http://localhost:8888"
	echo "phpmyadmin - http://localhost:8080"