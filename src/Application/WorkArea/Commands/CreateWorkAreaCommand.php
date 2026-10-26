<?php

declare(strict_types=1);

namespace Application\WorkArea\Commands;

use Application\Bus\Command\Command;
use Application\WorkArea\Data\WorkAreaData;

final class CreateWorkAreaCommand extends Command
{
    public function __construct(
        public readonly WorkAreaData $data,
    ) {
        parent::__construct();
    }
}
