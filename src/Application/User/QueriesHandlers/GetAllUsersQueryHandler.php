<?php

declare(strict_types=1);

namespace Application\User\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\User\Contracts\UserServiceContract;
use Application\User\Queries\GetAllUsersQuery;
use Illuminate\Database\Eloquent\Collection;
use InvalidArgumentException;

class GetAllUsersQueryHandler extends QueryHandler
{
    public function __construct(
        protected readonly UserServiceContract $users,
    ) {}

    public function handle(Query $query): Collection
    {
        if (! $query instanceof GetAllUsersQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetAllUsersQuery::class, $query::class));
        }

        return $this->users->all($query->getWith());
    }
}
