import 'package:dio/dio.dart';

class ApiRequestService{

  static final dio = Dio();

//   post requests
static Future<Response> post(String url, Map<String, dynamic> body) async {
  return  await dio.post(url,data:body,options: Options(headers: {
    'Accept' : 'application/json',
  }));
}

// get requests
  static Future<Response> get(String url, Map<String, dynamic> params) async {
    return  await dio.post(url,queryParameters:params,options: Options(headers: {
      'Accept' : 'application/json',
    }));
  }

// put requests

// delete requests
}