<?php

use App\Http\Controllers\Acl\Permission\PermissionController;
use App\Http\Controllers\Acl\Role\RoleController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\UserController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('login', [LoginController::class, 'login']);
    Route::group(['middleware' => ['auth:api', AdminMiddleware::class]], function () {
        Route::post('logout', [LoginController::class, 'logout']);
        Route::get('dashboard-details', [DashboardController::class, 'index']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
    });
});
