<?php

declare(strict_types=1);

namespace Application\WorkArea\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\WorkArea\Queries\GetWorkAreaQuery;
use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spatie\QueryBuilder\QueryBuilder;

final class GetWorkAreaQueryHandler extends QueryHandler
{
    public function handle(Query $query): WorkArea
    {
        if (! $query instanceof GetWorkAreaQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetWorkAreaQuery::class, $query::class));
        }

        /** @var WorkArea */
        return QueryBuilder::for(WorkArea::class, new Request($query->getParameters()))
            ->allowedIncludes(GetWorkAreasQueryHandler::INCLUDES)
            ->whereKey($query->workAreaId)
            ->firstOrFail();
    }
}
