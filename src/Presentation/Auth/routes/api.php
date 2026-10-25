<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\Auth\Controllers\AuthController;

Route::prefix('auth')->controller(AuthController::class)->group(function (): void {
    Route::post('login', 'login')->middleware('throttle:10,1');

    Route::middleware('auth:api')->group(function (): void {
        Route::post('logout', 'logout');
        Route::get('me', 'me');
    });
});
