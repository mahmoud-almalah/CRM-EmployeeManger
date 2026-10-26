<?php

declare(strict_types=1);

namespace Application\WorkShift\Commands;

use Application\Bus\Command\Command;

final class DeleteWorkShiftCommand extends Command
{
    public function __construct(
        public readonly string $workAreaId,
        public readonly string $shiftId,
    ) {
        parent::__construct();
    }
}
