<?php

declare(strict_types=1);

namespace Application\Supervisor\Queries;

use Application\Bus\Query\Query;

final class GetSupervisorQuery extends Query
{
    public function __construct(
        public readonly string $supervisorId,
    ) {
        parent::__construct();
    }
}
