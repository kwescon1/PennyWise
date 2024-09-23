// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'ApiResponse.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

ApiResponse<Type> _$ApiResponseFromJson<Type>(
  Map<String, dynamic> json,
  Type Function(Object? json) fromJsonType,
) =>
    ApiResponse<Type>(
      isSuccess: json['isSuccess'] as bool,
      message: json['message'] as String,
      data: _$nullableGenericFromJson(json['data'], fromJsonType),
    );

Map<String, dynamic> _$ApiResponseToJson<Type>(
  ApiResponse<Type> instance,
  Object? Function(Type value) toJsonType,
) =>
    <String, dynamic>{
      'isSuccess': instance.isSuccess,
      'message': instance.message,
      'data': _$nullableGenericToJson(instance.data, toJsonType),
    };

T? _$nullableGenericFromJson<T>(
  Object? input,
  T Function(Object? json) fromJson,
) =>
    input == null ? null : fromJson(input);

Object? _$nullableGenericToJson<T>(
  T? input,
  Object? Function(T value) toJson,
) =>
    input == null ? null : toJson(input);
