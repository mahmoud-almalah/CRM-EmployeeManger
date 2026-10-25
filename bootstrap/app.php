<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Presentation\Exceptions\ApiExceptionRenderer;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__.'/../src/Presentation/UserManagement/routes/web.php'],
        api: [
            __DIR__.'/../src/Presentation/Auth/routes/api.php',
            __DIR__.'/../src/Presentation/UserManagement/routes/api.php',
        ],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        ApiExceptionRenderer::register($exceptions);
    })->create();
