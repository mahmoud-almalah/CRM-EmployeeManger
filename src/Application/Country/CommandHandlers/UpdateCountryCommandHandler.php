<?php

declare(strict_types=1);

namespace Application\Country\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\Country\Commands\UpdateCountryCommand;
use Application\Country\Contracts\CountryServiceContract;
use Domain\Country\Entities\Country;
use InvalidArgumentException;

final class UpdateCountryCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly CountryServiceContract $countries,
    ) {}

    public function handle(Command $command): Country
    {
        if (! $command instanceof UpdateCountryCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', UpdateCountryCommand::class, $command::class));
        }

        return $this->countries->update($command->countryId, $command->data);
    }
}
