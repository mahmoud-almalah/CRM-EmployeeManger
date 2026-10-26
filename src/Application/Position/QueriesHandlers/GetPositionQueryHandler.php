<?php

declare(strict_types=1);

namespace Application\Position\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\Position\Queries\GetPositionQuery;
use Domain\Position\Entities\Position;
use InvalidArgumentException;

final class GetPositionQueryHandler extends QueryHandler
{
    public function handle(Query $query): Position
    {
        if (! $query instanceof GetPositionQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetPositionQuery::class, $query::class));
        }

        return Position::query()->whereKey($query->positionId)->firstOrFail();
    }
}
