<?php

declare(strict_types=1);

namespace Application\WorkArea\Commands;

use Application\Bus\Command\Command;

final class DeleteWorkAreaCommand extends Command
{
    public function __construct(
        public readonly string $workAreaId,
    ) {
        parent::__construct();
    }
}
