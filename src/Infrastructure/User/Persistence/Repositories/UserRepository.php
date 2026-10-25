<?php

declare(strict_types=1);

namespace Infrastructure\User\Persistence\Repositories;

use Application\Repository\Repository;
use Domain\User\Entities\User;

class UserRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(new User);
    }
}
