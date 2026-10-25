<?php

declare(strict_types=1);

namespace Application\User\Queries;

use Application\Bus\Query\Query;

final class GetAllUsersQuery extends Query
{
    public function __construct(
        public array $with = []
    ) {
        parent::__construct(['with' => $this->with]);
    }

    public function getWith(): array
    {
        return $this->with;
    }
}
