import 'package:flutter/material.dart';

/// The `AppStyles` class defines reusable text styles for the application.
/// This ensures consistent typography across the app, allowing easy
/// customization of common text styles, such as titles and headings.
class AppStyles {
  /// This method returns a bold text style for titles.
  ///
  /// The `size` parameter defines the font size, with a default value of 64.
  /// The `color` parameter defines the font color, defaulting to black.
  ///
  /// Example usage:
  ///
  /// ```dart
  /// Text('My Title', style: AppStyles.titleX(size: 32, color: Colors.blue))
  /// ```
  static TextStyle titleX({double size = 64, Color color = Colors.black}) {
    return TextStyle(color: color, fontSize: size, fontWeight: FontWeight.bold);
  }

  static TextStyle title1({double size = 32, Color color = Colors.black}) {
    return TextStyle(color: color, fontSize: size, fontWeight: FontWeight.bold);
  }

  static TextStyle title3({double size = 18, Color color = Colors.black}) {
    return TextStyle(
      color: color,
      fontSize: size,
    );
  }

  static TextStyle regular1({double size = 16, Color color = Colors.black, FontWeight weight = FontWeight.normal}) {
    return TextStyle(
      color: color,
      fontSize: size,
      fontWeight: weight
    );
  }


}
