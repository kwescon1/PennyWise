import 'package:flutter/material.dart';
import 'package:pennywise/resources/app_colours.dart';
import 'package:pennywise/resources/app_routes.dart';
import 'package:pennywise/resources/app_strings.dart';
import 'package:pennywise/resources/views/onboarding/splash.dart';
import 'package:pennywise/resources/views/onboarding/walkthrough.dart';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  // This widget is the root of your application.
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: AppStrings.appName,
      theme: ThemeData(

        colorScheme: ColorScheme.fromSeed(seedColor: AppColours.primaryColour),
        useMaterial3: true,
        fontFamily: 'Inter',
      ),
      // Initial route
      initialRoute: AppRoutes.splash,
      routes: {
        AppRoutes.splash: (context) =>  const SplashScreen(),// splash screen
        AppRoutes.walkthrough: (context) =>  const WalkThroughScreen()// walkthrough screen
      },
    );
  }
}

