# Web Apperlass Makefile
# Usage: make [target]

.PHONY: help build up down logs shell test deploy backup clean

# Default target
help: ## Show this help message
	@echo "Available targets:"
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Development
install: ## Install dependencies and setup environment
	cp .env.example .env
	composer install
	npm install
	php artisan key:generate
	php artisan storage:link

dev-up: ## Start development environment
	php artisan serve &
	npm run dev &

dev-down: ## Stop development environment
	pkill -f "php artisan serve" || true
	pkill -f "npm run dev" || true

##@ Docker
build: ## Build Docker images
	docker-compose build --no-cache

up: ## Start Docker containers
	docker-compose up -d

down: ## Stop Docker containers
	docker-compose down

restart: ## Restart Docker containers
	make down
	make up

logs: ## Show Docker logs
	docker-compose logs -f

shell: ## Access application container shell
	docker-compose exec app sh

##@ Database
migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

migrate-fresh: ## Fresh migrate with seeding
	docker-compose exec app php artisan migrate:fresh --seed

db-seed: ## Seed database
	docker-compose exec app php artisan db:seed

##@ Testing
test: ## Run all tests
	docker-compose exec app php artisan test

test-unit: ## Run unit tests only
	docker-compose exec app php artisan test --testsuite=Unit

test-feature: ## Run feature tests only
	docker-compose exec app php artisan test --testsuite=Feature

test-coverage: ## Run tests with coverage
	docker-compose exec app php artisan test --coverage

##@ Code Quality
lint: ## Run PHP linter
	docker-compose exec app ./vendor/bin/phpcs --standard=PSR12 app/

lint-fix: ## Fix PHP linting issues
	docker-compose exec app ./vendor/bin/phpcbf --standard=PSR12 app/

##@ Cache Management
cache-clear: ## Clear all caches
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear

cache-build: ## Build all caches
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache

##@ Deployment
deploy-staging: ## Deploy to staging
	./scripts/deploy.sh staging

deploy-production: ## Deploy to production
	./scripts/deploy.sh production

backup: ## Create backup
	./scripts/backup.sh

##@ Maintenance
health: ## Check application health
	curl -f http://localhost/health | jq .

monitor: ## Show real-time logs
	docker-compose logs -f app

clean: ## Clean up Docker resources
	docker-compose down -v
	docker system prune -f
	docker volume prune -f

##@ Assets
assets-build: ## Build frontend assets
	npm run build

assets-dev: ## Build frontend assets for development
	npm run dev

assets-watch: ## Watch and rebuild assets
	npm run dev