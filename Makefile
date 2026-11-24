.PHONY: help build up run down stop logs ps test clean

help: ## Show available commands
	@grep -E '^[a-z]+:.*##' $(MAKEFILE_LIST) | awk -F':.*## ' '{printf "  make %-8s %s\n", $$1, $$2}'

build: ## Build all Docker images
	docker compose build

up: ## Start the stack in the background
	docker compose up -d --build

run: up ## Alias for `make up`

down: ## Stop and remove containers (keeps data volume)
	docker compose down

stop: ## Stop containers without removing them
	docker compose stop

logs: ## Follow logs of all services
	docker compose logs -f

ps: ## Show container status / health
	docker compose ps

test: ## Smoke test both services
	curl -fsS http://localhost:8080/health && echo
	curl -fsS -X POST -H "Content-Type: application/json" -d '{"title":"Smoke test task"}' http://localhost:8080/tasks && echo
	curl -fsS -o /dev/null -w "Laravel /go-tasks -> HTTP %{http_code}\n" http://localhost:8000/go-tasks

clean: ## Remove containers, images and the data volume
	docker compose down -v --rmi local
