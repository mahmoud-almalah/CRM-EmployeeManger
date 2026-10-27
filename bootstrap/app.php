<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Presentation\Exceptions\ApiExceptionRenderer;
use Presentation\Http\Middleware\AssignRequestId;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [__DIR__.'/../src/Presentation/UserManagement/routes/web.php'],
        // Every module keeps its routes in src/Presentation/<Module>/routes/api.php.
        api: glob(__DIR__.'/../src/Presentation/*/routes/api.php') ?: [],
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(AssignRequestId::class);

        $middleware->alias([
            'permission' => PermissionMiddleware::class,
            'role' => RoleMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        ApiExceptionRenderer::register($exceptions);
    })->create();
