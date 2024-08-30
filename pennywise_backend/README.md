# PennyWise API Backend

Welcome to the **PennyWise** API backend, the server-side component of the PennyWise expense tracking application. This backend is built with Laravel and provides RESTful APIs for managing expenses, budgets, and other financial data. The backend is containerized using Docker, making it easy to set up and deploy alongside the Flutter frontend.

## Project Structure

```plaintext
pennywise_backend/
│
├── docker-files/
│   ├── nginx/
│   │   ├── certs/             # SSL certificates (to be generated locally)
│   │   └── ...
│   └── ...
├── docker-compose.yml         # Docker Compose file for orchestrating containers
├── app/                       # Laravel application files
├── .env.example               # Example environment configuration file
├── .env                       # Environment configuration file (to be set up)
├── README.md                  # This README file
└── ...                        # Other backend-related files
```

## Prerequisites

-   **Docker**: Ensure Docker is installed on your machine. [Install Docker](https://docs.docker.com/get-docker/)
-   **Make**: Make sure you have `make` installed, as it will be used to manage commands for the API.

## Setting Up the API Backend

### Step 1: SSL Certificate Setup

To secure communication between your frontend and backend, you need to generate SSL certificates. We use `mkcert` for this purpose.

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

    - Move the generated `.pem` and `.key` files to the `./pennywise_backend/docker-files/nginx/certs/` directory.

4. **Ensure Correct File Naming**:

    - After moving the files, ensure that the file names match those specified in your Nginx configuration and Docker Compose file. This is crucial for Nginx to correctly find and use the SSL certificates.

    Here is an example of the relevant Nginx server block configuration:

    ```nginx
    server {
        listen 443 ssl;
        index index.php index.html;

        server_name localhost;
        error_log /var/log/nginx/error.log;
        access_log /var/log/nginx/access.log;

        root /var/www/pennywise/public;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
            gzip_static on;
        }

        location ~ \.php$ {
            try_files $uri =404;
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_pass app:9000;
            fastcgi_index index.php;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_param PATH_INFO $fastcgi_path_info;
        }

        ssl_certificate /etc/nginx/certs/localhost+2.pem;     # Ensure the file name matches
        ssl_certificate_key /etc/nginx/certs/localhost+2-key.pem; # Ensure the file name matches
    }
    ```

    And here’s how the SSL volumes are defined in the Docker Compose file:

    ```yaml
    pennywise_webserver:
        build:
            context: .
            dockerfile: ./docker-files/nginx/Dockerfile
        container_name: pennywise_webserver
        restart: unless-stopped
        depends_on:
            - db
            - app

        ports:
            - "8888:80"
            - "8889:443"

        volumes:
            - ./:/var/www/pennywise
            - ./docker-files/nginx/conf.d/app.conf:/etc/nginx/conf.d/app.conf
            # Mount SSL certs
            - ./docker-files/nginx/certs/localhost+2.pem:/etc/nginx/certs/localhost+2.pem # Ensure the file name matches
            - ./docker-files/nginx/certs/localhost+2-key.pem:/etc/nginx/certs/localhost+2-key.pem # Ensure the file name matches

        networks:
            - pennywise
    ```

    > **Important**: The names of the SSL certificate and key files (e.g., `localhost+2.pem` and `localhost+2-key.pem`) must exactly match those specified in both the Nginx configuration and the Docker Compose volumes section. If the names differ, Nginx will not be able to find the certificates, and SSL will not work.

### Step 2: Environment Variables

Before setting up the backend, follow these steps to configure the environment:

1. **Copy the example `.env` file**:

    - Run the following command to create your `.env` file from the provided `.env.example`:

    ```bash
    cp .env.example .env
    ```

2. **Edit the `.env` file**:
    - Open the `.env` file and set the following environment variables:
    ```plaintext
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_username
    DB_PASSWORD=your_database_password
    ```

### Step 3: Setting Up and Running the API

1. **Set Up the API**:

    - Navigate to the root directory of the project and run the following command to build the Docker container and set up the API:

    ```bash
    make build-api
    ```

2. **Start the API**:

    - Start the API by running:

    ```bash
    make up-api
    ```

3. **Enter the API Shell and Install Dependencies**:

    - After the API container is up and running, you need to enter the API shell and install the necessary PHP dependencies using Composer:

    ```bash
    make shell
    composer install
    ```

4. **View Logs**:

    - You can monitor the real-time logs of the API container using:

    ```bash
    make logs-api
    ```

5. **Stop the API**:
    - When you are done, you can stop and remove the API container with:
    ```bash
    make down-api
    ```

## Communication with the Frontend

The API provides RESTful endpoints that the Flutter frontend interacts with. Ensure that the Flutter app is configured to communicate with the backend API, typically at `https://localhost:8889` (or the appropriate URL if deployed).

## Additional Commands

### Makefile Commands for the API

-   **build-api**: Build the Docker container for the PennyWise API without using the cache.
-   **up-api**: Start the PennyWise API Docker container in detached mode.
-   **down-api**: Stop and remove the PennyWise API Docker container.
-   **logs-api**: View real-time logs from the PennyWise API Docker container.
-   **shell**: Access the shell of the API container to run commands like `composer install`.

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request. Make sure to follow the code style guidelines and provide clear commit messages.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For inquiries, suggestions, or support, please reach out to us at support@pennywiseapp.com.
