<?php

declare(strict_types=1);

namespace Application\WorkArea\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\WorkArea\Queries\GetWorkAreasQuery;
use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class GetWorkAreasQueryHandler extends QueryHandler
{
    public const INCLUDES = ['city', 'country', 'manager'];

    public function handle(Query $query): LengthAwarePaginator
    {
        if (! $query instanceof GetWorkAreasQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetWorkAreasQuery::class, $query::class));
        }

        return QueryBuilder::for(WorkArea::class, new Request($query->getParameters()))
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::exact('city_id'),
                AllowedFilter::exact('country_id'),
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts(['name', 'status', 'created_at'])
            ->allowedIncludes(self::INCLUDES)
            ->defaultSort('name')
            ->paginate($query->perPage, page: $query->page);
    }
}
