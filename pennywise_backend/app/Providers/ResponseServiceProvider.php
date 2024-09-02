<?php

namespace App\Providers;

use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
    public function boot(ResponseFactory $response): void
    {
        JsonResource::withoutWrapping();

        $response->macro('success', function ($message = null, $data) {
            return response()->json([
                'message' => $message ?? __('app.operation_successful'),
                'data' => $data ?: null,
            ], \Illuminate\Http\Response::HTTP_OK);
        });

        $response->macro('created', function ($message = null, $data = null) {
            return response()->json([
                'message' => $message ?? __('app.resource_created'),
                'data' => $data ?: null,
            ], \Illuminate\Http\Response::HTTP_CREATED);
        });

        $response->macro('notfound', function ($error) {
            if ($error instanceof NotFoundHttpException) {
                $error = __('app.resource_not_found');
            }

            return response()->json([
                'error' => $error,
            ], \Illuminate\Http\Response::HTTP_NOT_FOUND);
        });

        $response->macro('error', function ($error, $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR) {
            // If the exception is a ValidationException, return the validation errors
            if ($error instanceof ValidationException) {
                return response()->json([
                    'message' => $error->getMessage(),
                    'errors' => $error->errors(),
                ], $statusCode);
            }

            // Default error handling
            return response()->json([
                'error' => $error instanceof \Exception ? $error->getMessage() : $error,
            ], $statusCode);
        });
    }
}
