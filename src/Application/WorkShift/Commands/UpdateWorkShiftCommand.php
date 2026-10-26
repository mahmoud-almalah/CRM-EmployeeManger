<?php

declare(strict_types=1);

namespace Application\WorkShift\Commands;

use Application\Bus\Command\Command;
use Application\WorkShift\Data\WorkShiftData;

final class UpdateWorkShiftCommand extends Command
{
    public function __construct(
        public readonly string $shiftId,
        public readonly WorkShiftData $data,
    ) {
        parent::__construct();
    }
}
