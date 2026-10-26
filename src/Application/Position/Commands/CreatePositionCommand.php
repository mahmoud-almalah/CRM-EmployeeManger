<?php

declare(strict_types=1);

namespace Application\Position\Commands;

use Application\Bus\Command\Command;
use Application\Position\Data\PositionData;

final class CreatePositionCommand extends Command
{
    public function __construct(
        public readonly PositionData $data,
    ) {
        parent::__construct();
    }
}
