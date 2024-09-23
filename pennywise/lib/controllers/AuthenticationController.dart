import 'package:dio/dio.dart';
import 'package:pennywise/resources/app_strings.dart';
import 'package:pennywise/services/api/api_request.dart';
import 'package:pennywise/services/api/api_routes.dart';

import '../models/ApiResponse.dart';

class AuthenticationController{

  static Future<ApiResponse> register(Map<String,dynamic> formData) async {
    try{
      final response = await ApiRequestService.post(ApiRoutes.registerRoute, formData);

      return ApiResponse(isSuccess:true,message: "testing");
    }on DioException catch (e) {
      final message = e.response?.data['message'] ?? AppStrings.generalErrorMessage;

      final errors = e.response?.data['errors'];

      return ApiResponse(isSuccess:false,message: message,errors:errors);
    }catch(e){
      // any other exception
      return ApiResponse(isSuccess:false,message: AppStrings.generalErrorMessage);
    }

  }
}