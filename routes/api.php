<?php

use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {
    Route::prefix('user')->group(function () {

        Route::middleware('guest')->group(function () {
            Route::post('store', [AuthController::class, 'store']);
            Route::post('login', [AuthController::class, 'login']);
        });

        Route::middleware('auth:api')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::patch('update', [AuthController::class, 'update']);
            Route::get('refresh', [AuthController::class, 'refresh']);
            Route::get('logout', [AuthController::class, 'logout']);
        });

    });
});
