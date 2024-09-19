import 'package:flutter/material.dart';
import 'package:pennywise/resources/app_colours.dart';
import 'package:pennywise/resources/app_spacing.dart';
import 'package:pennywise/resources/app_styles.dart';

import '../../../app_strings.dart';

class CheckboxInputComponent extends StatefulWidget {
  // The current value of the checkbox (true if checked, false if unchecked)
  final bool value;

  // A callback function that will be triggered when the checkbox value changes
  final ValueChanged<bool> onChanged;

  final Widget? label;

  final bool isEnabled;

  // Constructor for the widget, requiring 'value' and 'onChanged' parameters
  const CheckboxInputComponent(
      {super.key, required this.value, required this.onChanged, this.label,this.isEnabled = true});

  @override
  State<CheckboxInputComponent> createState() => _CheckboxInputComponentState();
}

class _CheckboxInputComponentState extends State<CheckboxInputComponent> {
  // The 'late' keyword means that 'value' will be initialized later (in initState)
  late bool value;

  // initState is called when the widget is first created in the widget tree
  @override
  void initState() {
    // Initialize the 'value' from the widget's 'value' property
    value = widget.value;

    // Always call the super class's initState to ensure proper initialization
    super.initState();
  }

  // The build method is responsible for rendering the checkbox
  @override
  Widget build(BuildContext context) {
    return Row(
      // mainAxisAlignment: MainAxisAlignment.spaceEvenly,
      children: [
        Transform.scale(
          scale: 1.5,
          child: Checkbox(
            activeColor: AppColours.primaryColour,
            checkColor: Colors.white,
            side: BorderSide(color: AppColours.primaryColour),
            value: value, // The current state of the checkbox (true or false)
            onChanged: widget.isEnabled ? (bool? newValue) {
              // Triggered when the checkbox is toggled
              // Update the state and notify Flutter to re-render the widget
              setState(() {
                value =
                    newValue!; // Update the local value (ensure non-null with !)
              });

              // Call the parent widget's onChanged callback to inform about the change
              widget.onChanged(value);
            } : null,
          ),
        ),
        if (widget.label != null) ...[
          AppSpacing.horizontal(size: 4),
          Expanded(
              child: widget.label!)
        ]
      ],
    );
  }
}
