<?php

declare(strict_types=1);

namespace Application\Country\Commands;

use Application\Bus\Command\Command;
use Application\Country\Data\CountryData;

final class CreateCountryCommand extends Command
{
    public function __construct(
        public readonly CountryData $data,
    ) {
        parent::__construct();
    }
}
