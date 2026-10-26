<?php

declare(strict_types=1);

namespace Application\WorkShift\Commands;

use Application\Bus\Command\Command;
use Application\WorkShift\Data\WorkShiftData;

final class CreateWorkShiftCommand extends Command
{
    public function __construct(
        public readonly WorkShiftData $data,
    ) {
        parent::__construct();
    }
}
