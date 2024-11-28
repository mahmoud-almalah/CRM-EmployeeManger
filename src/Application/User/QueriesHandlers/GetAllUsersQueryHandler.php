<?php

declare(strict_types=1);

namespace Application\User\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\User\Contracts\UserServiceContract;
use Illuminate\Database\Eloquent\Collection;

class GetAllUsersQueryHandler extends QueryHandler
{
    public function __construct(
        protected readonly UserServiceContract $repository,
    ) {}

    public function handle(Query $query): Collection
    {
        return $this->repository->all($query->getWith());
    }
}
