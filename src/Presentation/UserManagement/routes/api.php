<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\UserManagement\Controllers\UserController;

Route::middleware('auth:api')->controller(UserController::class)->group(function (): void {
    Route::get('users', 'index');
    Route::get('users/{id}', 'show');
});
