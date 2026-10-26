<?php

declare(strict_types=1);

namespace Application\Country\Queries;

use Application\Bus\Query\Query;

final class GetCountryQuery extends Query
{
    public function __construct(
        public readonly string $countryId,
    ) {
        parent::__construct();
    }
}
