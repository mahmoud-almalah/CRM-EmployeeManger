<?php

declare(strict_types=1);

namespace Application\WorkArea\Queries;

use Application\Bus\Query\Query;

final class GetWorkAreaQuery extends Query
{
    /**
     * @param  array<int, string>  $include
     */
    public function __construct(
        public readonly string $workAreaId,
        public readonly array $include = [],
    ) {
        parent::__construct(['include' => implode(',', $include)]);
    }
}
