import 'package:flutter/material.dart';
import 'package:pennywise/resources/views/components/form/text_input.dart';
import 'package:pennywise/resources/views/components/ui/button.dart';

import '../../app_spacing.dart';
import '../../app_strings.dart';
import '../../app_styles.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  State<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {

  // Define a list of input labels
  final List<String> inputLabels = [
    AppStrings.labelUsernameOrEmail,
    AppStrings.labelPassword,
  ];
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
      title: Text(AppStrings.login, style: AppStyles.appBarTitle(),),
        leading: InkWell(
          onTap: () => Navigator.of(context).pop(),
          child: const Icon(Icons.arrow_back),
        ),
      ),
      body: ListView(
        padding: const EdgeInsets.all(24),
        children: [
          // Use a for loop to iterate over inputLabels and generate form fields
          for(var label in inputLabels)...[
            TextInputComponent(label: label,isPassword: label == AppStrings.labelPassword, textEditingController: TextEditingController(),),
            AppSpacing.vertical(size: 16)
          ],
          AppSpacing.vertical(size: 16),
          ButtonComponent(
              label: AppStrings.login,
              onPressed: () => {}),

        ],
      ),
    );
  }
}
