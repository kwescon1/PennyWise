# PennyWise

**PennyWise** is a comprehensive expense tracking application designed to help you manage your finances effortlessly. The application consists of two main components: a Flutter-based mobile frontend and a Laravel-based API backend. Both components can be easily managed using a `Makefile` for streamlined development and deployment.

## Project Structure

```plaintext
pennywise/
│
├── pennywise_backend/     # Dockerized Laravel API backend
│   ├── dockerfiles
│   ├── docker-compose.yml
│   ├── app/              # Laravel application files
│   ├── README.md         # Detailed setup instructions for the backend
│   └── ...               # Other backend-related files
│
├── pennywise/            # Flutter frontend
│   ├── lib/
│   ├── android/
│   ├── ios/
│   └── ...               # Other frontend-related files
│
└── Makefile              # Root Makefile to manage both API and Flutter app
└── README.md             # This README file
```

## Features

### Mobile App (Flutter)

- **Expense Tracking**: Log and categorize your expenses on the go.
- **Budget Management**: Set and monitor your budget goals.
- **Spending Insights**: Analyze your spending patterns with detailed reports.
- **Cross-Platform**: Available for both Android and iOS.

### API (Laravel)

- **RESTful API**: Provides endpoints for managing expenses, categories, budgets, and more.
- **Authentication**: Secure user authentication and authorization.
- **Data Sync**: Handles data synchronization across multiple devices.

## Getting Started

### Prerequisites

- **Docker**: Ensure Docker is installed on your machine. [Install Docker](https://docs.docker.com/get-docker/)
- **Flutter SDK**: Ensure you have Flutter installed. [Install Flutter](https://flutter.dev/docs/get-started/install)
- **Make**: Make sure you have `make` installed, as it will be used to manage commands for both the API and the Flutter app.

### SSL Certificate Setup

To enable SSL for the Dockerized Laravel API, each developer needs to generate their own SSL certificates locally. We use `mkcert` for this purpose. Please follow these steps:

1. **Install mkcert** (if not already installed):

   - For macOS:
     ```bash
     brew install mkcert
     brew install nss # if you use Firefox
     ```
   - For Linux and Windows, follow the instructions at [mkcert's repository](https://github.com/FiloSottile/mkcert).

2. **Generate a Local Certificate**:

   ```bash
   mkcert -install
   mkcert localhost 127.0.0.1 ::1
   ```

3. **Place the Generated Files**:

   - Move the generated `.pem` and `.key` files to the appropriate directory within the project (e.g., `./pennywise_backend/certs/`).

4. **Configure Docker**:

   - Ensure the Docker container uses the correct paths for the SSL certificate and private key. This can be configured in the `docker-compose.yml` file.

5. **Start the Application**:
   ```bash
   make start
   ```

> **Important:** Do not push the SSL certificates or private keys to the repository. These files contain sensitive information and should remain secure on your local machine.

### Setting Up the Project

You can set up the entire project, including the API and Flutter app, using the `Makefile` commands.

1. **Set Up the Project**:
   This command will build the Docker container for the Laravel API and install the necessary dependencies for the Flutter app.

   ```bash
   make setup
   ```

2. **Start the Project**:
   This command will start the Laravel API container and run the Flutter app on your connected device or emulator.

   ```bash
   make start
   ```

3. **Stop the Project**:
   This command will stop the Laravel API container.
   ```bash
   make stop
   ```

### Makefile Commands

The `Makefile` in the root directory includes the following commands:

- **API Commands**:

  - `make build-api`: Build the Docker container for the Laravel API.
  - `make up-api`: Start the Laravel API Docker container.
  - `make down-api`: Stop the Laravel API Docker container.
  - `make logs-api`: View logs from the Laravel API Docker container.

- **Flutter Commands**:

  - `make flutter-get`: Install dependencies for the Flutter app.
  - `make flutter-run`: Run the Flutter app on a connected device or emulator.
  - `make flutter-build`: Build the Flutter app (e.g., APK for Android).
  - `make flutter-clean`: Clean the Flutter project (removes build artifacts).

- **Combined Commands**:
  - `make setup`: Set up the entire project (build the API and install Flutter dependencies).
  - `make start`: Start both the API and the Flutter app.
  - `make stop`: Stop the Laravel API container.

### Backend Setup

For a more complete setup and detailed configuration of the backend, please refer to the [README in the pennywise_backend directory](./pennywise_backend/README.md).

### Communication Between Frontend and Backend

The Flutter app communicates with the Laravel API via RESTful API calls. Ensure that your API endpoints are correctly configured in the Flutter app's codebase, pointing to `https://localhost:8889` (or the appropriate server URL if deployed).

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request. Make sure to follow the code style guidelines and provide clear commit messages.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For inquiries, suggestions, or support, please reach out to us at support@pennywiseapp.com.
