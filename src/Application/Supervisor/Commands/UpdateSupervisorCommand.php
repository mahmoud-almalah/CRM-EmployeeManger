<?php

declare(strict_types=1);

namespace Application\Supervisor\Commands;

use Application\Bus\Command\Command;
use Application\Supervisor\Data\SupervisorData;

final class UpdateSupervisorCommand extends Command
{
    public function __construct(
        public readonly string $supervisorId,
        public readonly SupervisorData $data,
    ) {
        parent::__construct();
    }
}
