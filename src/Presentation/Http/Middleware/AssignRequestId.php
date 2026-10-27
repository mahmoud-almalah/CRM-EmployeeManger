<?php

declare(strict_types=1);

namespace Presentation\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gives every request an id, puts it on every log line written while the
 * request runs and returns it in the X-Request-Id header, so a user's report
 * can be matched to the log.
 */
final class AssignRequestId
{
    public const HEADER = 'X-Request-Id';

    public function handle(Request $request, Closure $next): Response
    {
        $id = $this->idFrom($request);

        $request->headers->set(self::HEADER, $id);
        Log::shareContext(['request_id' => $id]);

        $response = $next($request);
        $response->headers->set(self::HEADER, $id);

        return $response;
    }

    /**
     * A client may send its own id so a call can be followed across services,
     * but only a short, plain one: the value ends up in logs and headers.
     */
    private function idFrom(Request $request): string
    {
        $sent = $request->header(self::HEADER);

        if (is_string($sent) && preg_match('/^[A-Za-z0-9._-]{8,64}$/', $sent) === 1) {
            return $sent;
        }

        return (string) Str::uuid();
    }
}
