<?php

declare(strict_types=1);

namespace Application\Country\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\Country\Queries\GetCountriesQuery;
use Domain\Country\Entities\Country;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class GetCountriesQueryHandler extends QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        if (! $query instanceof GetCountriesQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetCountriesQuery::class, $query::class));
        }

        return QueryBuilder::for(Country::class, new Request($query->getParameters()))
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::exact('currency'),
            ])
            ->allowedSorts(['name', 'phone_code', 'created_at'])
            ->defaultSort('name')
            ->paginate($query->perPage, page: $query->page);
    }
}
