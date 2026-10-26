<?php

declare(strict_types=1);

namespace Infrastructure\Supervisor\Persistence\Repositories;

use Application\Repository\Repository;
use Application\Supervisor\Contracts\SupervisorRepositoryContract;
use Domain\Supervisor\Entities\Supervisor;

class SupervisorRepository extends Repository implements SupervisorRepositoryContract
{
    public function __construct()
    {
        parent::__construct(new Supervisor);
    }
}
