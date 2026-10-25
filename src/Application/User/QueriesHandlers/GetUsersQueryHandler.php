<?php

declare(strict_types=1);

namespace Application\User\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\User\Queries\GetUsersQuery;
use Domain\User\Entities\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class GetUsersQueryHandler extends QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        if (! $query instanceof GetUsersQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetUsersQuery::class, $query::class));
        }

        return QueryBuilder::for(User::class, new Request($query->getParameters()))
            ->allowedFilters([
                AllowedFilter::exact('status'),
                AllowedFilter::exact('position_id'),
                AllowedFilter::exact('work_area_id'),
                AllowedFilter::exact('is_remote'),
                AllowedFilter::exact('is_freelance'),
                AllowedFilter::callback('search', $this->search(...)),
            ])
            ->allowedSorts(['name', 'username', 'salary', 'start_working_date', 'created_at'])
            ->allowedIncludes(['roles'])
            ->defaultSort('-created_at')
            ->paginate($query->perPage, page: $query->page);
    }

    /**
     * Matches the username, the email or any part of the name. Wildcards in the
     * search term are escaped so they are matched literally.
     */
    private function search(Builder $builder, mixed $value): void
    {
        $term = '%'.addcslashes(is_array($value) ? implode(' ', $value) : (string) $value, '%_'.chr(92)).'%';

        $builder->where(function (Builder $group) use ($term): void {
            $group->where('username', 'like', $term)
                ->orWhere('email', 'like', $term)
                ->orWhere('name', 'like', $term);
        });
    }
}
