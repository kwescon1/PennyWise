# PennyWise Frontend (Flutter)

**PennyWise** is a mobile expense tracking application that helps users manage their finances with ease. This README covers the setup and usage of the Flutter frontend. The backend is powered by a Laravel API and Dockerized separately.

## Project Overview

The Flutter app includes:

- **Expense Tracking**: Log and categorize your expenses.
- **Budget Management**: Set budgets for each category.
- **Financial Insights**: View detailed reports of your spending.
- **User Authentication**: Secure sign-up and login functionality.

## Features

- Cross-platform support for both **Android** and **iOS**.
- Clean UI to track and manage expenses with categories and financial reports.
- Integration with a Dockerized Laravel backend.

## Prerequisites

Before running the Flutter app, ensure you have the following installed:

- **Flutter SDK**: [Install Flutter](https://flutter.dev/docs/get-started/install)
- **Android Studio or Xcode**: For running the app on a device or emulator.
- **Backend**: The Laravel API must be running. See the [backend README](../pennywise_backend/README.md) for setup instructions.

## Getting Started

1. **Clone the Repository**:

   ```bash
   git clone [the repository](https://github.com/kwescon1/PennyWise.git)
   cd Pennywise/pennywise
   ```

2. **Install Flutter Dependencies**:
   Run the following command to install the required packages:

   ```bash
   flutter pub get
   ```

3. **Configure API URL**:
   Update the API base URL in the app’s configuration files to point to the correct backend endpoint. Example:

   ```dart
   const String apiUrl = "https://localhost:8889/api";
   ```

4. **Run the Flutter App**:
   Connect a device or start an emulator, then run the app using:

   ```bash
   flutter run
   ```

5. **Testing the App**:
   You can also run tests using:
   ```bash
   flutter test
   ```

## Folder Structure

Here’s a breakdown of the main folders and files:

```
pennywise_frontend/
├── lib/
│   ├── main.dart          # Entry point of the Flutter app
│   ├── models/            # Data models (e.g., expenses, categories)
│   ├── resources/         # App resources like views, components, etc.
│       ├── views/         # UI views (e.g., home, login, signup)
│           ├── components/  # Reusable UI components like buttons
│           └── ...        # Other view-related folders and files
└── pubspec.yaml           # Flutter dependencies and project metadata
```

## Communication with Backend

The app communicates with the Laravel backend via RESTful API calls. Ensure the backend is running, and the API endpoints are correctly configured.

### Example API Call

```dart
Future<void> fetchExpenses() async {
  final response = await http.get('$apiUrl/expenses');
  // Handle response
}
```

## Debugging

1. **Run the app in debug mode**:

   ```bash
   flutter run --debug
   ```

2. **Check logs**:
   If you face issues, check logs in the terminal or through Android Studio’s logcat.

## Building for Production

To build a production version of the app, use the following commands:

- **For Android**:

  ```bash
  flutter build apk --release
  ```

- **For iOS**:
  ```bash
  flutter build ios --release
  ```

## Contributing

We welcome contributions! If you’d like to contribute:

- Fork the repository.
- Create a feature branch.
- Submit a pull request with clear commit messages.

## License

This project is licensed under the MIT License. See the [LICENSE](../LICENSE) file for details.

## Contact

For support or inquiries, feel free to reach us at support@pennywiseapp.com.
