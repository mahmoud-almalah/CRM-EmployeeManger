<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Query\Query;

final class GetUsersQuery extends Query
{
    /**
     * @param  array<string, mixed>  $filters
     * @param  array<int, string>  $include
     */
    public function __construct(
        public readonly array $filters = [],
        public readonly ?string $sort = null,
        public readonly array $include = [],
        public readonly int $page = 1,
        public readonly int $perPage = 15,
    ) {
        parent::__construct([
            'filter' => $filters,
            'sort' => $sort,
            'include' => implode(',', $include),
        ]);
    }
}
