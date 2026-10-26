<?php

declare(strict_types=1);

namespace Application\Supervisor\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\Supervisor\Queries\GetSupervisorsQuery;
use Domain\Supervisor\Entities\Supervisor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class GetSupervisorsQueryHandler extends QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        if (! $query instanceof GetSupervisorsQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetSupervisorsQuery::class, $query::class));
        }

        return QueryBuilder::for(Supervisor::class, new Request($query->getParameters()))
            ->allowedFilters([
                AllowedFilter::partial('name'),
                AllowedFilter::partial('email'),
                AllowedFilter::exact('status'),
            ])
            ->allowedSorts(['name', 'email', 'status', 'created_at'])
            ->defaultSort('name')
            ->paginate($query->perPage, page: $query->page);
    }
}
