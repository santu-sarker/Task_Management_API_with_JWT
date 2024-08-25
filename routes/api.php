<?php

use App\Http\Controllers\Auth\JWTAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where we can register our API routes for the application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::middleware(['throttle:api'])->prefix('auth')->group(function () {
    Route::post('login', [JWTAuthController::class, 'login']);
    Route::post('register', [JWTAuthController::class, 'register']);

    Route::middleware(['auth:api'])->group(function () {

        Route::post('refresh', [JWTAuthController::class, 'refresh']);
        Route::post('logout', [JWTAuthController::class, 'logout']);

        Route::get("/test", function () {
            return response()->json([
                "data" => "success",
            ]);
        });
    });
});

Route::middleware(['auth:api', 'storeModelName', 'throttle:api'])->group(function () {
    // task related routes
    Route::apiResource('tasks', TaskController::class);

    // categories related routes
    Route::apiResource('categories', CategoryController::class);
});
