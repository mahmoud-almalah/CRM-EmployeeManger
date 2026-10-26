<?php

declare(strict_types=1);

namespace Application\City\Queries;

use Application\Bus\Query\Query;

final class GetCityQuery extends Query
{
    /**
     * @param  array<int, string>  $include
     */
    public function __construct(
        public readonly string $cityId,
        public readonly array $include = [],
    ) {
        parent::__construct(['include' => implode(',', $include)]);
    }
}
