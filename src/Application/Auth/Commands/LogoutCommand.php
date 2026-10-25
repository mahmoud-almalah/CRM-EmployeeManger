<?php

declare(strict_types=1);

namespace Application\Auth\Commands;

use Application\Bus\Command\Command;
use Domain\User\Entities\User;

final class LogoutCommand extends Command
{
    public function __construct(
        public readonly User $user,
    ) {
        parent::__construct();
    }
}
