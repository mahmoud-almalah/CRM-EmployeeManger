<?php

declare(strict_types=1);

namespace Application\User\Commands;

use Application\Bus\Command\Command;

final class DeleteUserCommand extends Command
{
    public function __construct(
        public readonly string $userId,
        public readonly string $requestedBy,
    ) {
        parent::__construct();
    }
}
