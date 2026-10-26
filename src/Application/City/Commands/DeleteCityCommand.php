<?php

declare(strict_types=1);

namespace Application\City\Commands;

use Application\Bus\Command\Command;

final class DeleteCityCommand extends Command
{
    public function __construct(
        public readonly string $cityId,
    ) {
        parent::__construct();
    }
}
