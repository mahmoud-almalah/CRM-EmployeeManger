<?php

declare(strict_types=1);

namespace Infrastructure\User\Persistence\Repositories;

use Application\Repository\Repository;
use Application\User\Contracts\UserRepositoryContract;
use Domain\User\Entities\User;

class UserRepository extends Repository implements UserRepositoryContract
{
    public function __construct()
    {
        parent::__construct(new User);
    }

    public function findByLogin(string $login): ?User
    {
        return User::query()
            ->where('username', $login)
            ->orWhere('email', strtolower(trim($login)))
            ->first();
    }
}
