# Define directories
API_DIR = ./pennywise_backend
FLUTTER_DIR = ./pennywise

# Define a symbol for recursively expanding variables (used for better performance)
.ONESHELL:

# Define the default target
help: ## Print help
	@echo -e "\nUsage:\n  make \033[36m<target>\033[0m\n"
	@echo -e "Targets:\n"
	@awk 'BEGIN {FS = ":.*##"; printf ""} \
	/^[a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-15s\033[0m %s\n", $$1, $$2 }' $(MAKEFILE_LIST)

# API commands
build-api: ## Build the Docker container for the pennywise API without using cache
	@echo "Building the pennywise API Docker container..."
	cd $(API_DIR) && docker-compose build --no-cache

up-api: ## Start the pennywise API Docker container in detached mode
	@echo "Starting the pennywise API Docker container..."
	cd $(API_DIR) && docker-compose up -d

down-api: ## Stop and remove the pennywise API Docker container
	@echo "Stopping the pennywise API Docker container..."
	cd $(API_DIR) && docker-compose down

logs-api: ## Show real-time logs from the pennywise API Docker container
	@echo "Showing logs from the pennywise API Docker container..."
	cd $(API_DIR) && docker-compose logs -f

shell: ## Access the shell of the API container
	@echo "Entering API shell..."
	cd $(API_DIR) && docker exec -it -u ubuntu pennywise /bin/bash

# Flutter commands
flutter-get: ## Install Flutter dependencies specified in pubspec.yaml
	@echo "Running flutter pub get..."
	cd $(FLUTTER_DIR) && flutter pub get

flutter-run: ## Run the Flutter app on a connected device or emulator
	@echo "Running the Flutter app..."
	cd $(FLUTTER_DIR) && flutter run

flutter-build: ## Build the Flutter app APK for Android
	@echo "Building the Flutter app..."
	cd $(FLUTTER_DIR) && flutter build apk

flutter-clean: ## Clean the Flutter project by removing build artifacts
	@echo "Cleaning the Flutter project..."
	cd $(FLUTTER_DIR) && flutter clean

# Combined commands
setup: ## Set up the project by building the API and installing Flutter dependencies
	@echo "Setting up the project (API and Flutter)..."
	make build-api
	make flutter-get

start: ## Start the entire project including API and Flutter app
	@echo "Starting the project (API and Flutter)..."
	make up-api
	make flutter-run

stop: ## Stop the entire project by shutting down the API
	@echo "Stopping the project (API and Flutter)..."
	make down-api