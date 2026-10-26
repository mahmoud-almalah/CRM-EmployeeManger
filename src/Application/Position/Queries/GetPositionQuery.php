<?php

declare(strict_types=1);

namespace Application\Position\Queries;

use Application\Bus\Query\Query;

final class GetPositionQuery extends Query
{
    public function __construct(
        public readonly string $positionId,
    ) {
        parent::__construct();
    }
}
