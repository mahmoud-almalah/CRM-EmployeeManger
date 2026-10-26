<?php

declare(strict_types=1);

namespace Application\City\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\City\Queries\GetCitiesQuery;
use Domain\City\Entities\City;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class GetCitiesQueryHandler extends QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        if (! $query instanceof GetCitiesQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetCitiesQuery::class, $query::class));
        }

        return QueryBuilder::for(City::class, new Request($query->getParameters()))
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::exact('country_id'),
            ])
            ->allowedSorts(['name', 'code', 'created_at'])
            ->allowedIncludes(['country'])
            ->defaultSort('name')
            ->paginate($query->perPage, page: $query->page);
    }
}
