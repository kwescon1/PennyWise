import 'package:flutter/material.dart';
import 'package:pennywise/resources/app_colours.dart';
import 'package:pennywise/resources/app_spacing.dart';

import '../../../app_strings.dart';

class TextInputComponent extends StatefulWidget {
  final bool isEnabled;
  final bool isRequired;
  final String label;
  final bool isPassword;
  final TextEditingController textEditingController;
  final ValueChanged<String> ?onFieldSubmitted;
  final TextInputAction ?textInputAction;
  final FocusNode? focusNode;
  final TextInputType? textInputType;

  const TextInputComponent(
      {super.key, required this.label, this.isPassword = false, required this.textEditingController, this.onFieldSubmitted, this.textInputAction, this.focusNode, this.textInputType, this.isRequired = false, this.isEnabled = true});

  @override
  State<TextInputComponent> createState() => _TextInputComponentState();
}

class _TextInputComponentState extends State<TextInputComponent> {
  bool showPassword = true;

  @override
  Widget build(BuildContext context) {
    return TextFormField(
      enabled: widget.isEnabled,
      autovalidateMode: AutovalidateMode.onUserInteraction,
      validator: (value){
        if(!widget.isRequired) return null;
        if(value == null || value.isEmpty){
          return '${widget.label} ${AppStrings.fieldIsRequired}';}
        return null;
      },
      keyboardType: widget.textInputType,
      focusNode: widget.focusNode,
      onFieldSubmitted: widget.onFieldSubmitted,
      textInputAction: widget.textInputAction ?? TextInputAction.next,
      obscureText: (widget.isPassword && showPassword),
      decoration: InputDecoration(
        labelText: widget.label,
        labelStyle: TextStyle(color: AppColours.light20),
        border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: BorderSide(color: AppColours.light20.withOpacity(0.5))),
        enabledBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: BorderSide(color: AppColours.light20.withOpacity(0.5))),
        focusedBorder: OutlineInputBorder(
            borderRadius: BorderRadius.circular(16),
            borderSide: BorderSide(color: AppColours.primaryColour)),
        suffixIcon: widget.isPassword
            ? InkWell(
          onTap: togglePassword,
          child: Icon(
            showPassword
                ? Icons.visibility_off_outlined
                : Icons.visibility_outlined,
            color: AppColours.light20,
          ),
        )
            : AppSpacing.empty(),
      ),
    );
  }

  void togglePassword() => setState(() {
    showPassword = !showPassword;
  });
}
