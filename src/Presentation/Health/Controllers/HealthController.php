<?php

declare(strict_types=1);

namespace Presentation\Health\Controllers;

use Application\Bus\Contracts\QueryBusContract;
use Application\Health\Queries\GetHealthQuery;
use Illuminate\Http\JsonResponse;
use Presentation\Controller;

final class HealthController extends Controller
{
    public function __construct(private readonly QueryBusContract $queries) {}

    public function __invoke(): JsonResponse
    {
        /** @var array{healthy: bool, checks: array<string, string>} $report */
        $report = $this->queries->ask(new GetHealthQuery);

        return response()->json([
            'status' => $report['healthy'] ? 'ok' : 'degraded',
            'checks' => $report['checks'],
        ], $report['healthy'] ? 200 : 503);
    }
}
