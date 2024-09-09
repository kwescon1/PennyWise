# Authentication API Documentation

This document outlines the authentication routes for the application.

### Table of Contents

-   [Register](#register)
-   [Login](#login)
-   [Reset OTP](#reset-otp)
-   [Reset Password](#reset-password)
-   [Request OTP](#request-otp)
-   [Verify OTP](#verify-otp)

---

## Register

### **Endpoint:**

`POST /register`

### **Description:**

Registers a new user and generates an authentication token. Sends an OTP to the user's email for verification.

### **Request:**

-   **Body Parameters:**
    -   `firstname`: _string, required_ — User's first name.
    -   `lastname`: _string, required_ — User's last name.
    -   `email`: _string, required, unique_ — Valid email address for the user.
    -   `username`: _string, required, unique_ — Username for the user.
    -   `password`: _string, required, confirmed_ — Password for the user.
    -   `password_confirmation`: _string, required_ — Confirm the password.
-   **Example:**

```json
{
    "firstname": "John",
    "lastname": "Doe",
    "email": "john@example.com",
    "username": "john_doe",
    "password": "Password123!",
    "password_confirmation": "Password123!"
}
```

### **Response:**

-   **Success**: HTTP `201 Created`
    -   User object and auth token

```json
{
    "user": {
        "id": "uuid string",
        "firstname": "John",
        "lastname": "Doe",
        "email": "john@example.com",
        "username": "john_doe"
    },
    "token": "eyJhbGciOiJIUzI1NiIsIn..."
}
```

-   **Error (Validation Failure)**: HTTP `422 Unprocessable Entity`
    ```json
    {
        "message": "The given data was invalid.",
        "errors": {
            "email": ["The email has already been taken."],
            "password": ["The password confirmation does not match."]
        }
    }
    ```

---

## Login

### **Endpoint:**

`POST /login`

### **Description:**

Authenticates an existing user and generates an authentication token.

### **Request:**

-   **Body Parameters:**

    -   `login`: _string, required_ — Username or email of the user.
    -   `password`: _string, required_ — User’s password.

-   **Example:**

```json
{
    "login": "john@example.com",
    "password": "Password123!"
}
```

### **Response:**

-   **Success**: HTTP `200 OK`
    -   User object and auth token

```json
{
    "user": {
        "id": "uuid string",
        "firstname": "John",
        "lastname": "Doe",
        "email": "john@example.com",
        "username": "john_doe"
    },
    "token": "eyJhbGciOiJIUzI1NiIsIn..."
}
```

-   **Error (Invalid Credentials)**: HTTP `401 Unauthorized`
    ```json
    {
        "message": "These credentials do not match our records."
    }
    ```

---

## Reset OTP

### **Endpoint:**

`POST /reset/otp`

### **Description:**

Request an OTP to reset the user's password. The OTP will be sent via email.

### **Request:**

-   **Body Parameters:**

    -   `login`: _string, required_ — Username or email of the user.

-   **Example:**

```json
{
    "login": "john@example.com"
}
```

### **Response:**

-   **Success**: HTTP `200 OK`

```json
{
    "message": "OTP has been sent to your email address."
}
```

-   **Error (User Not Found)**: HTTP `422 Unprocessable Entity`
    ```json
    {
        "message": "The given data was invalid.",
        "errors": {
            "login": [
                "We couldn't find an account with that username or email."
            ]
        }
    }
    ```

---

## Reset Password

### **Endpoint:**

`POST /reset/password`

### **Description:**

Resets the user's password after verifying the OTP. The OTP must be valid and associated with the user.

### **Request:**

-   **Body Parameters:**

    -   `otp`: _string, required_ — OTP sent to the user.
    -   `password`: _string, required, confirmed_ — New password for the user.
    -   `password_confirmation`: _string, required_ — Confirm the new password.

-   **Example:**

```json
{
    "otp": "123456",
    "password": "NewPassword123!",
    "password_confirmation": "NewPassword123!"
}
```

### **Response:**

-   **Success**: HTTP `200 OK`

```json
{
    "message": "Your password has been successfully reset."
}
```

-   **Error (Invalid OTP)**: HTTP `422 Unprocessable Entity`
    ```json
    {
        "message": "The given data was invalid.",
        "errors": {
            "otp": ["The provided OTP is invalid."]
        }
    }
    ```

---

## Request OTP (Requires Authentication)

### **Endpoint:**

`POST /otp`

### **Description:**

Sends a new OTP for verification. This is used when the user requests a new OTP to verify their account.

### **Request:**

-   **Headers**:

    -   `Authorization`: Bearer token (sanctum required)

-   **Example:**

```json
{}
```

### **Response:**

-   **Success**: HTTP `200 OK`

```json
{
    "message": "A new OTP has been sent to your email."
}
```

-   **Error (Too Many Requests)**: HTTP `429 Too Many Requests`
    ```json
    {
        "message": "You have exceeded the allowed number of attempts. Please try again later."
    }
    ```

---

## Verify OTP (Requires Authentication)

### **Endpoint:**

`POST /verify`

### **Description:**

Verifies the OTP sent to the user’s email and activates the user's account.

### **Request:**

-   **Body Parameters:**

    -   `otp`: _string, required_ — OTP sent to the user.

-   **Example:**

```json
{
    "otp": "123456"
}
```

### **Response:**

-   **Success**: HTTP `200 OK`

```json
{
    "message": "Your account has been successfully verified."
}
```

-   **Error (Invalid OTP)**: HTTP `422 Unprocessable Entity`
    ```json
    {
        "message": "The given data was invalid.",
        "errors": {
            "otp": ["The provided OTP is invalid."]
        }
    }
    ```
