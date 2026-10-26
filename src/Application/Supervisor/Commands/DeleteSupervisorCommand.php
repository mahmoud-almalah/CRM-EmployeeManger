<?php

declare(strict_types=1);

namespace Application\Supervisor\Commands;

use Application\Bus\Command\Command;

final class DeleteSupervisorCommand extends Command
{
    public function __construct(
        public readonly string $supervisorId,
    ) {
        parent::__construct();
    }
}
