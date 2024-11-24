PHP_COMMAND = php
SCRIPT = index.php

migrate-up:
	@$(PHP_COMMAND) $(SCRIPT) migrate:up

migrate-down:
	@$(PHP_COMMAND) $(SCRIPT) migrate:down
