<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Presentation\Organization\Controllers\CityController;
use Presentation\Organization\Controllers\CountryController;
use Presentation\Organization\Controllers\PositionController;
use Presentation\Organization\Controllers\WorkAreaController;

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

    Route::controller(PositionController::class)->prefix('positions')->group(function (): void {
        Route::get('/', 'index')->middleware('permission:positions.view,api');
        Route::get('{id}', 'show')->whereNumber('id')->middleware('permission:positions.view,api');
        Route::post('/', 'store')->middleware('permission:positions.create,api');
        Route::put('{id}', 'update')->whereNumber('id')->middleware('permission:positions.update,api');
        Route::delete('{id}', 'destroy')->whereNumber('id')->middleware('permission:positions.delete,api');
    });

    Route::controller(WorkAreaController::class)->prefix('work-areas')->group(function (): void {
        Route::get('/', 'index')->middleware('permission:work-areas.view,api');
        Route::get('{id}', 'show')->whereNumber('id')->middleware('permission:work-areas.view,api');
        Route::post('/', 'store')->middleware('permission:work-areas.create,api');
        Route::put('{id}', 'update')->whereNumber('id')->middleware('permission:work-areas.update,api');
        Route::delete('{id}', 'destroy')->whereNumber('id')->middleware('permission:work-areas.delete,api');
    });
});
