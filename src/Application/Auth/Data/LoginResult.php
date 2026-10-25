<?php

declare(strict_types=1);

namespace Application\Auth\Data;

use Domain\User\Entities\User;

final readonly class LoginResult
{
    public function __construct(
        public string $token,
        public User $user,
    ) {}
}
