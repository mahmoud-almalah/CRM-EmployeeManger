<?php

declare(strict_types=1);

namespace Application\WorkArea\Commands;

use Application\Bus\Command\Command;
use Application\WorkArea\Data\WorkAreaData;

final class UpdateWorkAreaCommand extends Command
{
    public function __construct(
        public readonly string $workAreaId,
        public readonly WorkAreaData $data,
    ) {
        parent::__construct();
    }
}
