<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // $exceptions->renderable(function //(NotFoundException | NotFoundHttpException $e) {
        //     return response()->notfound($e);
        // });

        $exceptions->renderable(function (NotFoundHttpException $e) {
            return response()->notfound($e->getMessage());
        });

        $exceptions->renderable(function (AuthenticationException $e) {
            return response()->error($e, Response::HTTP_UNAUTHORIZED);
        });


        $exceptions->renderable(function (Exception $e) {
            Log::error($e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->error($e);
        });
    })->create();
