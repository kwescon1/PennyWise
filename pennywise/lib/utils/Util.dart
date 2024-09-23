import 'package:flutter/material.dart';
import 'package:pennywise/resources/app_colours.dart';

import '../resources/app_styles.dart';

class Util{
  static snackBar(context,{required String message,bool isSuccess = true}){
    return ScaffoldMessenger.of(context).showSnackBar(new SnackBar(
        backgroundColor: isSuccess ? AppColours.primaryColour : Colors.red.shade500,
        content: Text(
          message,
          style: AppStyles.snackBar(),
        )));
  }
}