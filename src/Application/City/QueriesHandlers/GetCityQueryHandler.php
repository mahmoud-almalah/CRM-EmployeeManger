<?php

declare(strict_types=1);

namespace Application\City\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\City\Queries\GetCityQuery;
use Domain\City\Entities\City;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spatie\QueryBuilder\QueryBuilder;

final class GetCityQueryHandler extends QueryHandler
{
    public function handle(Query $query): City
    {
        if (! $query instanceof GetCityQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetCityQuery::class, $query::class));
        }

        /** @var City */
        return QueryBuilder::for(City::class, new Request($query->getParameters()))
            ->allowedIncludes(['country'])
            ->whereKey($query->cityId)
            ->firstOrFail();
    }
}
