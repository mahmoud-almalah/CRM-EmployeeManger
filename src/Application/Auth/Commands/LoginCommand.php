<?php

declare(strict_types=1);

namespace Application\Auth\Commands;

use Application\Bus\Command\Command;

final class LoginCommand extends Command
{
    public function __construct(
        public readonly string $login,
        #[\SensitiveParameter]
        public readonly string $password,
    ) {
        parent::__construct();
    }
}
