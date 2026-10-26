<?php

declare(strict_types=1);

namespace Application\Supervisor\Commands;

use Application\Bus\Command\Command;
use Application\Supervisor\Data\SupervisorData;

final class CreateSupervisorCommand extends Command
{
    public function __construct(
        public readonly SupervisorData $data,
    ) {
        parent::__construct();
    }
}
