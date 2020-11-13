<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\HttpLogger\Middlewares\HttpLogger;
use App\Http\Controllers\API\v1\Admin\Auth\AuthController;
use App\Http\Controllers\API\v1\PromoCode\PromoCodeController;

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

Route::prefix('v1')->namespace('v1')->middleware(HttpLogger::class)->group(function () {

    Route::prefix('admin')->namespace('Admin')->group(function () {

        Route::namespace('Auth')->group(function () {

            Route::post('/register', [AuthController::class, 'register']);

            Route::post('/login', [AuthController::class, 'login']);
        });
    });

    Route::prefix('promocodes')->namespace('PromoCode')->middleware('auth:sanctum')->group(function () {

        Route::get('/', [PromoCodeController::class, 'index']);

        Route::post('/create', [PromoCodeController::class, 'store']);

        Route::post('/update/{id}', [PromoCodeController::class, 'update']);

        Route::post('/delete/{id}', [PromoCodeController::class, 'destroy']);

        Route::get('/active', [PromoCodeController::class, 'active']);

        Route::get('/expired', [PromoCodeController::class, 'expired']);

        Route::post('/deactivate/{id}', [PromoCodeController::class, 'deactivate']);

        Route::get('/deactivated', [PromoCodeController::class, 'deactivated']);
    });


    Route::post('promocodes/verify/{code}', [PromoCodeController::class, 'show']);
});