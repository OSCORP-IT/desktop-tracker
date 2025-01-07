<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ----------------------- public page route section ----------------------- //
Route::get('/', [PublicPageController::class, 'index']);

// ----------------------- ADMIN panel route section ----------------------- //
Route::middleware('auth')->group(function() {
    Route::prefix('admin-panel')->group(function() {
        Route::get('/dashboard', [AdminPanel\DashboardController::class, 'index']);

        Route::resource('projects', AdminPanel\ProjectController::class);
        Route::resource('tasks', AdminPanel\TaskController::class);
        Route::resource('users', AdminPanel\UserController::class);

        Route::get('/my-account', [AdminPanel\MyAccountController::class, 'my_account']);
        Route::get('/my-account-edit', [AdminPanel\MyAccountController::class, 'my_account_edit']);
        Route::put('/my-account-update', [AdminPanel\MyAccountController::class, 'my_account_update']);

        Route::get('/system-settings', [AdminPanel\SystemSettingsController::class, 'index']);
    });
});

require __DIR__.'/auth.php';
