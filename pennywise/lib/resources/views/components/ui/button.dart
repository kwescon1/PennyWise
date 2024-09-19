import 'package:flutter/material.dart';
import 'package:pennywise/resources/app_colours.dart';
import 'package:pennywise/resources/app_styles.dart';

class ButtonComponent extends StatefulWidget {
  final String label;
  final Widget? icon;
  final ButtonType type;
  final double? width;
  final Function() onPressed;
  final bool isLoading;

  const ButtonComponent(
      {super.key,
      required this.label,
      this.icon,
      this.type = ButtonType.primary,
      this.width,
      required this.onPressed,  this.isLoading = false});

  @override
  State<ButtonComponent> createState() => _ButtonComponentState();
}

class _ButtonComponentState extends State<ButtonComponent> {
  final Map<ButtonType, Color> backgroundColor = {
    ButtonType.primary: AppColours.primaryColour,
    ButtonType.secondary: AppColours.primaryColourLight,
    ButtonType.light: Colors.white,
  };

  final Map<ButtonType, Color> foregroundColor = {
    ButtonType.primary: Colors.white,
    ButtonType.secondary: AppColours.primaryColour,
    ButtonType.light: Colors.black,
  };

  final Map<ButtonType, Color> borderColor = {
    ButtonType.primary: Colors.white,
    ButtonType.secondary: AppColours.primaryColour,
    ButtonType.light: AppColours.primaryColourLight.withOpacity(0.5),
  };

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: widget.width ??
          MediaQuery.of(context)
              .size
              .width, // fill up the entire width of the device
      height: 56,
      child: ElevatedButton(
          onPressed: () {
            if(!widget.isLoading) widget.onPressed();
          },
          style: ElevatedButton.styleFrom(
            elevation: 0,
            shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
                side: BorderSide(color: borderColor[widget.type]!)),
            backgroundColor: backgroundColor[widget.type],
          ),
          child: widget.isLoading ?  CircularProgressIndicator(color:  foregroundColor[widget.type]): Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              if(widget.icon != null ) widget.icon!,
              Text(
                widget.label,
                style: AppStyles.title3(color: foregroundColor[widget.type]!),
              )
            ],
          )),
    );
  }
}

enum ButtonType { primary, secondary, light }
