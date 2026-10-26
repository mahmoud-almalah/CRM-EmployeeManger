<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\Organization\Controllers\CityController;
use Presentation\Organization\Controllers\CountryController;

Route::middleware('auth:api')->group(function (): void {
    Route::controller(CountryController::class)->prefix('countries')->group(function (): void {
        Route::get('/', 'index')->middleware('permission:countries.view,api');
        Route::get('{id}', 'show')->whereNumber('id')->middleware('permission:countries.view,api');
        Route::post('/', 'store')->middleware('permission:countries.create,api');
        Route::put('{id}', 'update')->whereNumber('id')->middleware('permission:countries.update,api');
        Route::delete('{id}', 'destroy')->whereNumber('id')->middleware('permission:countries.delete,api');
    });

    Route::controller(CityController::class)->prefix('cities')->group(function (): void {
        Route::get('/', 'index')->middleware('permission:cities.view,api');
        Route::get('{id}', 'show')->whereNumber('id')->middleware('permission:cities.view,api');
        Route::post('/', 'store')->middleware('permission:cities.create,api');
        Route::put('{id}', 'update')->whereNumber('id')->middleware('permission:cities.update,api');
        Route::delete('{id}', 'destroy')->whereNumber('id')->middleware('permission:cities.delete,api');
    });
});
