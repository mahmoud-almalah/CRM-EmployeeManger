<?php

declare(strict_types=1);

namespace Application\Country\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\Country\Queries\GetCountryQuery;
use Domain\Country\Entities\Country;
use InvalidArgumentException;

final class GetCountryQueryHandler extends QueryHandler
{
    public function handle(Query $query): Country
    {
        if (! $query instanceof GetCountryQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetCountryQuery::class, $query::class));
        }

        return Country::query()->whereKey($query->countryId)->firstOrFail();
    }
}
