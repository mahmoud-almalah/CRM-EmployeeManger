<?php

declare(strict_types=1);

namespace Application\Position\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\Position\Queries\GetPositionsQuery;
use Domain\Position\Entities\Position;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class GetPositionsQueryHandler extends QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        if (! $query instanceof GetPositionsQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetPositionsQuery::class, $query::class));
        }

        return QueryBuilder::for(Position::class, new Request($query->getParameters()))
            ->allowedFilters([
                AllowedFilter::partial('name'),
            ])
            ->allowedSorts(['name', 'created_at'])
            ->defaultSort('name')
            ->paginate($query->perPage, page: $query->page);
    }
}
