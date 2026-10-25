<?php

declare(strict_types=1);

namespace Application\User\Contracts;

use Application\Repository\RepositoryInterface;
use Domain\User\Entities\User;

interface UserRepositoryContract extends RepositoryInterface
{
    /**
     * Finds an account by username or email.
     */
    public function findByLogin(string $login): ?User;
}
