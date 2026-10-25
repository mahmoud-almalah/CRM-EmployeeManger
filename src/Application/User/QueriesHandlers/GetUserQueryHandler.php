<?php

declare(strict_types=1);

namespace Application\User\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\User\Queries\GetUserQuery;
use Domain\User\Entities\User;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spatie\QueryBuilder\QueryBuilder;

final class GetUserQueryHandler extends QueryHandler
{
    public function handle(Query $query): User
    {
        if (! $query instanceof GetUserQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetUserQuery::class, $query::class));
        }

        /** @var User */
        return QueryBuilder::for(User::class, new Request($query->getParameters()))
            ->allowedIncludes(['roles'])
            ->whereKey($query->userId)
            ->firstOrFail();
    }
}
