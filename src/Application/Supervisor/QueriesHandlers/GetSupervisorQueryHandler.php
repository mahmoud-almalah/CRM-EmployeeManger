<?php

declare(strict_types=1);

namespace Application\Supervisor\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\Supervisor\Queries\GetSupervisorQuery;
use Domain\Supervisor\Entities\Supervisor;
use InvalidArgumentException;

final class GetSupervisorQueryHandler extends QueryHandler
{
    public function handle(Query $query): Supervisor
    {
        if (! $query instanceof GetSupervisorQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetSupervisorQuery::class, $query::class));
        }

        return Supervisor::query()->whereKey($query->supervisorId)->firstOrFail();
    }
}
