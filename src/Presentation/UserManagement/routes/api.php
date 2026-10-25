<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\UserManagement\Controllers\UserController;

Route::middleware('auth:api')->controller(UserController::class)->group(function (): void {
    Route::get('users', 'index')->middleware('permission:users.view,api');
    Route::get('users/{id}', 'show')->middleware('permission:users.view,api');
    Route::post('users', 'store')->middleware('permission:users.create,api');
    Route::put('users/{id}', 'update')->middleware('permission:users.update,api');
    Route::delete('users/{id}', 'destroy')->middleware('permission:users.delete,api');
});
