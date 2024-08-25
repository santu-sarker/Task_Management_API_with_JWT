<?php

use App\Http\Middleware\storeModelName;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('api', [
            storeModelName::class,
        ]);

        $middleware->alias([
            'storeModelName' => storeModelName::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });

        $exceptions->renderable(function (ModelNotFoundException $e, $request) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
        });

        $exceptions->renderable(function (AuthenticationException $e, $request) {
            return response()->json(['error' =>  $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        });
        $exceptions->renderable(function (Throwable $e, $request) {
            return response()->json(['error' =>  $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
