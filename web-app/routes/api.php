<?php

namespace App\Http\Controllers;

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

Route::prefix('employee-panel')->group(function() {
    Route::post('login', [API\V1\EmployeePanel\AuthenticationController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function() {
        Route::prefix('dashboard')->group(function() {
            Route::get('index', [API\V1\EmployeePanel\DashboardController::class, 'index']);

            Route::get('my-account', [API\V1\EmployeePanel\ProfileController::class, 'my_account']);
            Route::post('change-password', [API\V1\EmployeePanel\ProfileController::class, 'change_password']);
            Route::post('profile-image-update', [API\V1\EmployeePanel\ProfileController::class, 'profile_image_update']);
            Route::post('my-account-update', [API\V1\EmployeePanel\ProfileController::class, 'my_account_update']);
        });
    });
});
