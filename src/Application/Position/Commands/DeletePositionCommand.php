<?php

declare(strict_types=1);

namespace Application\Position\Commands;

use Application\Bus\Command\Command;

final class DeletePositionCommand extends Command
{
    public function __construct(
        public readonly string $positionId,
    ) {
        parent::__construct();
    }
}
