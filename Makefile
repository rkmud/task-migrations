DOCKER_CONTAINER=migrations-php-1
PHP_SCRIPT_PATH=app/execute-migrations.php

migrate-up:
	docker exec -it $(DOCKER_CONTAINER) php $(PHP_SCRIPT_PATH) --direction=up

migrate-down:
	docker exec -it $(DOCKER_CONTAINER) php $(PHP_SCRIPT_PATH) --direction=down
