import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';
import 'package:pennywise/resources/app_routes.dart';
import 'package:pennywise/resources/app_spacing.dart';
import 'package:pennywise/resources/app_strings.dart';
import 'package:pennywise/resources/app_styles.dart';
import 'package:pennywise/resources/views/components/form/checkbox_input.dart';
import 'package:pennywise/resources/views/components/form/text_input.dart';

import '../../app_colours.dart';
import '../components/ui/button.dart';

class SignupScreen extends StatefulWidget {
  const SignupScreen({super.key});

  @override
  State<SignupScreen> createState() => _SignupScreenState();
}

class _SignupScreenState extends State<SignupScreen> {



  // Define a list of input labels and associated focus nodes
  final List<Map<String, dynamic>> inputFields = [
    {
      'label': AppStrings.labelFirstName,
      'controller': TextEditingController(),
      'focusNode': FocusNode(),
      'textInputType': TextInputType.name,
      'isRequired': true,
    },
    {
      'label': AppStrings.labelLastName,
      'controller': TextEditingController(),
      'focusNode': FocusNode(),
      'textInputType': TextInputType.name,
      'isRequired': true,
    },
    {
      'label': AppStrings.labelUsername,
      'controller': TextEditingController(),
      'focusNode': FocusNode(),
      'isRequired': true,
    },
    {
      'label': AppStrings.labelEmail,
      'controller': TextEditingController(),
      'focusNode': FocusNode(),
      'textInputType': TextInputType.emailAddress,
      'isRequired': true,
    },
    {
      'label': AppStrings.labelPassword,
      'controller': TextEditingController(),
      'focusNode': FocusNode(),
      'isRequired': true,
    },
    {
      'label': AppStrings.labelConfirmPassword,
      'controller': TextEditingController(),
      'focusNode': FocusNode(),
      'textInputAction': TextInputAction.done,
      'isRequired': true,
    },
  ];

  final formKey = GlobalKey<FormState>();
  bool isLoading = false;

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap:() => FocusScope.of(context).unfocus(),
      child: Scaffold(
        backgroundColor: AppColours.bgColor,
        appBar: AppBar(
          centerTitle: true,
          backgroundColor: AppColours.bgColor,
          title: Text(
            AppStrings.signUp,
            style: AppStyles.appBarTitle(),
          ),
          leading: InkWell(
            onTap: () => Navigator.of(context).pop(),
            child: const Icon(Icons.arrow_back),
          ),
        ),
        body: Form(key:formKey, child: ListView(
          padding: const EdgeInsets.all(24),
          children: <Widget>[
        // Use a for loop to iterate over input fields and generate form fields dynamically
            for (var i = 0; i< inputFields.length; i++) ...[
              TextInputComponent(
                isEnabled: !isLoading,
                  isRequired: inputFields[i].containsKey('isRequired') ? inputFields[i]['isRequired']: false,
        textEditingController: inputFields[i]['controller'],
                label: inputFields[i]['label'],
                isPassword: inputFields[i]['label'] == AppStrings.labelPassword ||
                    inputFields[i]['label'] == AppStrings.labelConfirmPassword,
        focusNode: inputFields[i]['focusNode'],
            textInputType:inputFields[i].containsKey('textInputType') ? inputFields[i]['textInputAction']: TextInputType.text,
                  // Check if 'textInputAction' key exists in the current map
                  textInputAction: inputFields[i].containsKey('textInputAction')
                      ? inputFields[i]['textInputAction']
                      : TextInputAction.next, // Provide a default value if not available
          onFieldSubmitted: (value) {
            // Move to the next field if available, otherwise unfocused
            if(i < inputFields.length - 1){
              FocusScope.of(context).requestFocus(inputFields[i + 1]['focusNode']);
            }else{
              FocusScope.of(context).unfocus(); // Unfocus if it's the last field
            }
          }
              ),
              AppSpacing.vertical(size: 16)
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
                              size: 14, color: AppColours.primaryColour))
                    ])),
                value: false,
                onChanged: (value) => print(value)),
            AppSpacing.vertical(),
            ButtonComponent(
              isLoading: isLoading,
                label: AppStrings.signUp, onPressed:signup),

            AppSpacing.vertical(size: 16),
            Text(AppStrings.orWith,
                textAlign: TextAlign.center,
                style: AppStyles.bold(size: 14, color: AppColours.light20)),
            AppSpacing.vertical(size: 16),
            ButtonComponent(
                icon: Padding(
                  padding: EdgeInsets.all(16),
                  child: Image.asset("assets/images/google.png"),
                ),
                type: ButtonType.light,
                label: AppStrings.signUpWithGoogle,
                onPressed: () => {print("sigin up")}),
            AppSpacing.vertical(size: 16),
            Text.rich(
                textAlign: TextAlign.center,
                style: AppStyles.medium(size: 16),
                TextSpan(text: AppStrings.alreadyHaveAnAccount, children: [
                  WidgetSpan(child: AppSpacing.horizontal(size: 4)),
                  TextSpan(
                      recognizer: TapGestureRecognizer()..onTap = () => Navigator.of(context).pushNamed(AppRoutes.login),
                      text: AppStrings.login,
                      style: AppStyles.medium(
                          size: 16, color: AppColours.primaryColour).copyWith(decoration: TextDecoration.underline,decorationColor: AppColours.primaryColour))
                ])),
          ],
        )),
      ),
    );
  }

  void signup(){
    FocusScope.of(context).unfocus();
    if(!formKey.currentState!.validate()){
      return;
    }
    setState(() => isLoading= true);

    Future.delayed(const Duration(seconds: 5), ()  {
      setState(() => isLoading= false);
    print("success");
    });

  }
}
