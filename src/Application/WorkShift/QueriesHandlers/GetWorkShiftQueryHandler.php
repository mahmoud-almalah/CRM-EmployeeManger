<?php

declare(strict_types=1);

namespace Application\WorkShift\QueriesHandlers;

use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\WorkShift\Contracts\WorkShiftServiceContract;
use Application\WorkShift\Queries\GetWorkShiftQuery;
use Domain\WorkShift\Entities\WorkShift;
use InvalidArgumentException;

final class GetWorkShiftQueryHandler extends QueryHandler
{
    public function __construct(
        private readonly WorkShiftServiceContract $shifts,
    ) {}

    public function handle(Query $query): WorkShift
    {
        if (! $query instanceof GetWorkShiftQuery) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', GetWorkShiftQuery::class, $query::class));
        }

        return $this->shifts->findInWorkArea($query->workAreaId, $query->shiftId);
    }
}
