<?php

declare(strict_types=1);

namespace Application\City\Commands;

use Application\Bus\Command\Command;
use Application\City\Data\CityData;

final class CreateCityCommand extends Command
{
    public function __construct(
        public readonly CityData $data,
    ) {
        parent::__construct();
    }
}
