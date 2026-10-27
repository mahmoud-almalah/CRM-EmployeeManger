<?php

declare(strict_types=1);

namespace Application\Health\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\Health\Contracts\HealthProbeContract;
use Application\Health\Queries\GetHealthQuery;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Throwable;

final class GetHealthQueryHandler extends QueryHandler
{
    /** @var array<int, HealthProbeContract> */
    private readonly array $probes;

    public function __construct(HealthProbeContract ...$probes)
    {
        $this->probes = $probes;
    }

    /**
     * @return array{healthy: bool, checks: array<string, string>}
     */
    public function handle(Query $query): array
    {
        if (! $query instanceof GetHealthQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetHealthQuery::class, $query::class));
        }

        $checks = [];

        foreach ($this->probes as $probe) {
            try {
                $probe->check();
                $checks[$probe->name()] = 'ok';
            } catch (Throwable $e) {
                // The cause goes to the log; the response only says which check failed.
                Log::error('Health check failed.', ['check' => $probe->name(), 'exception' => $e]);
                $checks[$probe->name()] = 'failed';
            }
        }

        return [
            'healthy' => ! in_array('failed', $checks, true),
            'checks' => $checks,
        ];
    }
}
