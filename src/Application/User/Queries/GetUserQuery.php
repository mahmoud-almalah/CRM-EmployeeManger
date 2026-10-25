<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Query\Query;

final class GetUserQuery extends Query
{
    /**
     * @param  array<int, string>  $include
     */
    public function __construct(
        public readonly string $userId,
        public readonly array $include = [],
    ) {
        parent::__construct(['include' => implode(',', $include)]);
    }
}
