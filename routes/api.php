<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\HttpLogger\Middlewares\HttpLogger;
use App\Http\Controllers\API\v1\Admin\Auth\AuthController;

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

Route::prefix('v1')->namespace('v1')->middleware(HttpLogger::class)->group(function() {

    Route::prefix('/admin')->namespace('Admin')->group(function() {

        Route::namespace('Auth')->group(function() {

            Route::post('/register', [AuthController::class, 'register']);

            Route::post('/login', [AuthController::class, 'login']);

        });

    });

});