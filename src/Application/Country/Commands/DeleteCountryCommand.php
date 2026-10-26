<?php

declare(strict_types=1);

namespace Application\Country\Commands;

use Application\Bus\Command\Command;

final class DeleteCountryCommand extends Command
{
    public function __construct(
        public readonly string $countryId,
    ) {
        parent::__construct();
    }
}
