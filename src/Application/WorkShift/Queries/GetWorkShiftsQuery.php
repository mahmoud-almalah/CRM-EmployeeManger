<?php

declare(strict_types=1);

namespace Application\WorkShift\Queries;

use Application\Bus\Query\Query;

final class GetWorkShiftsQuery extends Query
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        public readonly string $workAreaId,
        public readonly array $filters = [],
        public readonly ?string $sort = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
    ) {
        parent::__construct([
            'filter' => $filters,
            'sort' => $sort,
        ]);
    }
}
