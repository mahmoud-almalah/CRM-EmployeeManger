<?php

declare(strict_types=1);

namespace Application\WorkShift\Queries;

use Application\Bus\Query\Query;

final class GetWorkShiftQuery extends Query
{
    public function __construct(
        public readonly string $workAreaId,
        public readonly string $shiftId,
    ) {
        parent::__construct();
    }
}
