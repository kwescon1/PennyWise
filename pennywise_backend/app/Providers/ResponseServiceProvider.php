<?php

namespace App\Providers;

use Illuminate\Http\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Resources\Json\JsonResource;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        Response::macro('success', function ($data) {
            return response()->json([
                'data' => $data ?: null,
            ], \Illuminate\Http\Response::HTTP_OK);
        });

        Response::macro('created', function ($data) {
            return response()->json([
                'data' => $data ?: null,
            ], \Illuminate\Http\Response::HTTP_CREATED);
        });

        Response::macro('notfound', function ($error) {
            return response()->json([
                'error' => $error,

            ], \Illuminate\Http\Response::HTTP_NOT_FOUND);
        });

        Response::macro('error', function ($error, $statusCode = \Illuminate\Http\Response::HTTP_INTERNAL_SERVER_ERROR) {
            return response()->json([
                'error' => $error,
                'status' => $statusCode,

            ], $statusCode);
        });
    }
}
