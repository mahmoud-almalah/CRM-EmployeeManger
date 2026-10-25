<?php

declare(strict_types=1);

namespace Presentation\Exceptions;

use Application\Auth\Exceptions\AccountNotActiveException;
use Application\Auth\Exceptions\InvalidCredentialsException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Every API error has the same body: {"message": "...", "errors": {...}?}.
 */
final class ApiExceptionRenderer
{
    public static function register(Exceptions $exceptions): void
    {
        $exceptions->shouldRenderJsonWhen(
            static fn (Request $request): bool => $request->is('api/*') || $request->expectsJson()
        );

        $exceptions->render(static function (Throwable $e, Request $request): ?JsonResponse {
            if (! ($request->is('api/*') || $request->expectsJson())) {
                return null;
            }

            return self::render($e);
        });
    }

    public static function render(Throwable $e): JsonResponse
    {
        return match (true) {
            $e instanceof ValidationException => self::json('The given data was invalid.', 422, $e->errors()),
            $e instanceof ModelNotFoundException,
            $e->getPrevious() instanceof ModelNotFoundException => self::json('Resource not found.', 404),
            $e instanceof AuthenticationException => self::json('Unauthenticated.', 401),
            $e instanceof InvalidCredentialsException => self::json($e->getMessage(), 401),
            $e instanceof AccountNotActiveException => self::json($e->getMessage(), 403),
            $e instanceof AuthorizationException => self::json($e->getMessage() ?: 'This action is unauthorized.', 403),
            $e instanceof HttpExceptionInterface => self::json(
                $e->getMessage() !== '' ? $e->getMessage() : 'HTTP error.',
                $e->getStatusCode(),
            ),
            default => self::json(
                config('app.debug') ? $e->getMessage() : 'Server error.',
                500,
            ),
        };
    }

    /**
     * @param  array<string, array<int, string>>|null  $errors
     */
    private static function json(string $message, int $status, ?array $errors = null): JsonResponse
    {
        $body = ['message' => $message];

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return new JsonResponse($body, $status);
    }
}
