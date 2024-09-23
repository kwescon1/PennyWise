import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:pennywise/controllers/AuthenticationController.dart';
import 'package:pennywise/resources/app_routes.dart';
import 'package:pennywise/resources/app_spacing.dart';
import 'package:pennywise/resources/app_strings.dart';
import 'package:pennywise/resources/app_styles.dart';
import 'package:pennywise/resources/views/components/form/checkbox_input.dart';
import 'package:pennywise/resources/views/components/form/text_input.dart';

import '../../../utils/Util.dart';
import '../../app_colours.dart';
import '../components/ui/button.dart';

class SignupScreen extends StatefulWidget {
  const SignupScreen({super.key});

  @override
  State<SignupScreen> createState() => _SignupScreenState();
}

class _SignupScreenState extends State<SignupScreen> {
  final _formKey = GlobalKey<FormState>();

  bool isLoading = false;
  bool hasAgreed = false;

  Map<String, dynamic> errors = {};

  final _firstnameEditingController = TextEditingController();
  final _lastnameEditingController = TextEditingController();
  final _emailEditingController = TextEditingController();
  final _usernameEditingController = TextEditingController();
  final _passwordEditingController = TextEditingController();
  final _confirmPasswordEditingController = TextEditingController();

  List<String> formDataKeys = [
    'firstname',
    'lastname',
    'username',
    'email',
    'password',
    'password_confirmation',
  ];

  late List<Map<String, dynamic>> inputFields;

  @override
  void initState() {
    super.initState();

    // Initialize inputFields in initState where instance members can be accessed
    inputFields = [
      {
        'label': AppStrings.labelFirstName,
        'controller': _firstnameEditingController,
        'focusNode': FocusNode(),
        'textInputType': TextInputType.name,
        'isRequired': true,
      },
      {
        'label': AppStrings.labelLastName,
        'controller': _lastnameEditingController,
        'focusNode': FocusNode(),
        'textInputType': TextInputType.name,
        'isRequired': true,
      },
      {
        'label': AppStrings.labelUsername,
        'controller': _usernameEditingController,
        'focusNode': FocusNode(),
        'isRequired': true,
      },
      {
        'label': AppStrings.labelEmail,
        'controller': _emailEditingController,
        'focusNode': FocusNode(),
        'textInputType': TextInputType.emailAddress,
        'isRequired': true,
      },
      {
        'label': AppStrings.labelPassword,
        'controller': _passwordEditingController,
        'focusNode': FocusNode(),
        'isRequired': true,
        'textInputType': TextInputType.visiblePassword,
      },
      {
        'label': AppStrings.labelConfirmPassword,
        'controller': _confirmPasswordEditingController,
        'focusNode': FocusNode(),
        'textInputAction': TextInputAction.done,
        'isRequired': true,
        'textInputType': TextInputType.visiblePassword,
      },
    ];
  }

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: () => FocusScope.of(context).unfocus(),
      child: Scaffold(
        backgroundColor: AppColours.bgColor,
        appBar: AppBar(
          centerTitle: true,
          backgroundColor: AppColours.bgColor,
          title: Text(
            AppStrings.signUp,
            style: AppStyles.appBarTitle(),
          ),
          leading: IconButton(
            onPressed: () => Navigator.of(context).pop(),
            icon: const Icon(Icons.arrow_back),
          ),
        ),
        body: Form(
          key: _formKey,
          child: ListView(
            padding: const EdgeInsets.all(24),
            children: <Widget>[
              AppSpacing.vertical(size: 48),
              _signupForm(), // Calling the extracted _signupForm method
            ],
          ),
        ),
      ),
    );
  }

  @override
  void dispose() {
    _firstnameEditingController.dispose();
    _lastnameEditingController.dispose();
    _usernameEditingController.dispose();
    _emailEditingController.dispose();
    _passwordEditingController.dispose();
    _confirmPasswordEditingController.dispose();

    super.dispose();
  }

  Widget _signupForm() {
    return Column(
      children: [
        // Use a for loop to iterate over input fields and generate form fields dynamically
        for (var i = 0; i < inputFields.length; i++) ...[
          TextInputComponent(
            error: errors[formDataKeys[i]]?.join(', '),
            isEnabled: !isLoading,
            isRequired: inputFields[i].containsKey('isRequired')
                ? inputFields[i]['isRequired']
                : false,
            textEditingController: inputFields[i]['controller'],
            label: inputFields[i]['label'],
            isPassword: inputFields[i]['label'] == AppStrings.labelPassword ||
                inputFields[i]['label'] == AppStrings.labelConfirmPassword,
            focusNode: inputFields[i]['focusNode'],
            textInputType: inputFields[i].containsKey('textInputType')
                ? inputFields[i]['textInputType'] // Corrected key
                : TextInputType.text,
            // Check if 'textInputAction' key exists in the current map
            textInputAction: inputFields[i].containsKey('textInputAction')
                ? inputFields[i]['textInputAction']
                : TextInputAction
                    .next, // Provide a default value if not available
            onFieldSubmitted: (value) {
              // Move to the next field if available, otherwise unfocused
              if (i < inputFields.length - 1) {
                FocusScope.of(context)
                    .requestFocus(inputFields[i + 1]['focusNode']);
              } else {
                FocusScope.of(context)
                    .unfocus(); // Unfocus if it's the last field
              }
            },
          ),
          AppSpacing.vertical(size: 16),
        ],
        CheckboxInputComponent(
          isEnabled: !isLoading,
          label: Text.rich(
            style: AppStyles.medium(size: 14),
            TextSpan(text: AppStrings.agreeText, children: [
              WidgetSpan(child: AppSpacing.horizontal(size: 4)),
              TextSpan(
                text: AppStrings.termsAndPrivacy,
                style: AppStyles.medium(
                  size: 14,
                  color: AppColours.primaryColour,
                ),
              ),
            ]),
          ),
          value: hasAgreed,
          onChanged: (value) => setState(() => hasAgreed = value
          ),
        ),
        AppSpacing.vertical(),
        ButtonComponent(
          isLoading: isLoading,
          label: AppStrings.signUp,
          onPressed: signup,
        ),
        AppSpacing.vertical(size: 16),
        Text(
          AppStrings.orWith,
          textAlign: TextAlign.center,
          style: AppStyles.bold(size: 14, color: AppColours.light20),
        ),
        AppSpacing.vertical(size: 16),
        ButtonComponent(
          icon: Padding(
            padding: EdgeInsets.all(16),
            child: Image.asset("assets/images/google.png"),
          ),
          type: ButtonType.light,
          label: AppStrings.signUpWithGoogle,
          onPressed: () => {print("sign up")},
        ),
        AppSpacing.vertical(size: 16),
        Text.rich(
          textAlign: TextAlign.center,
          style: AppStyles.medium(size: 16),
          TextSpan(
            text: AppStrings.alreadyHaveAnAccount,
            style: AppStyles.medium(color: AppColours.light20),
            children: [
              WidgetSpan(child: AppSpacing.horizontal(size: 4)),
              TextSpan(
                recognizer: TapGestureRecognizer()
                  ..onTap =
                      () => Navigator.of(context).pushNamed(AppRoutes.login),
                text: AppStrings.login,
                style: AppStyles.medium(
                  size: 16,
                  color: AppColours.primaryColour,
                ).copyWith(
                  decoration: TextDecoration.underline,
                  decorationColor: AppColours.primaryColour,
                ),
              ),
            ],
          ),
        ),
      ],
    );
  }

  void signup() async {
    //close the keyboard
    FocusScope.of(context).unfocus();

    // validate form
    if (!_formKey.currentState!.validate()) {
      return;
    }

    if(!hasAgreed){
      return Util.snackBar(context, message: AppStrings.inputIsRequired.replaceAll(":input", AppStrings.termsAndPrivacy), isSuccess: false);
    }
    // Show loading state
    setState(() => isLoading = true);

    // declare a map of formdata
    Map<String, dynamic> formData = {};

    for (var i = 0; i < inputFields.length; i++) {
      // Check if the current field is 'password' or 'password_confirmation'
      if (formDataKeys[i] == 'password' ||
          formDataKeys[i] == 'password_confirmation') {
        // Don't trim for password fields
        formData[formDataKeys[i]] = inputFields[i]['controller'].text;
      } else {
        // Trim for all other fields
        formData[formDataKeys[i]] = inputFields[i]['controller'].text.trim();
      }
    }
    var response = await AuthenticationController.register(formData);

    setState(() => isLoading = false);

    if (!response.isSuccess) {
      Util.snackBar(context, message: response.message, isSuccess: false);

      if (response.errors != null) {
        errors = response.errors!;
      }

      return;
    }

    // if there is no error, redirect to verification page
    Navigator.of(context).pushNamed(AppRoutes.verification);
  }
}
