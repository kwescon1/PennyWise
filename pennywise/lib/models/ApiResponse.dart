import 'package:json_annotation/json_annotation.dart';

part 'ApiResponse.g.dart';

@JsonSerializable(genericArgumentFactories: true)
class ApiResponse<Type> {
  final bool isSuccess;
  final String message;
  Type? data;

  Map<String,dynamic>? errors;

  ApiResponse({required this.isSuccess, required this.message, this.data,this.errors});

  /// Connect the generated [_$ApiResponseFromJson] function to the `fromJson`
  /// factory.
  factory ApiResponse.fromJson(Map<String, dynamic> json, Type Function(Object? json),fromJsonType) => _$ApiResponseFromJson(json,fromJsonType);

  /// Connect the generated [_$ApiResponseToJson] function to the `toJson` method.
  Map<String, dynamic> toJson(Object Function(Type value) toJsonType) => _$ApiResponseToJson(this,toJsonType);

}