<?php

declare(strict_types=1);

namespace Application\WorkShift\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\WorkShift\Queries\GetWorkShiftsQuery;
use Domain\WorkArea\Entities\WorkArea;
use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

final class GetWorkShiftsQueryHandler extends QueryHandler
{
    public function handle(Query $query): LengthAwarePaginator
    {
        if (! $query instanceof GetWorkShiftsQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetWorkShiftsQuery::class, $query::class));
        }

        WorkArea::query()->findOrFail($query->workAreaId);

        return QueryBuilder::for(WorkShift::query()->where('work_area_id', $query->workAreaId), new Request($query->getParameters()))
            ->allowedFilters([
                AllowedFilter::exact('name'),
            ])
            ->allowedSorts(['name', 'start_time', 'end_time', 'created_at'])
            ->defaultSort('start_time')
            ->paginate($query->perPage, page: $query->page);
    }
}
