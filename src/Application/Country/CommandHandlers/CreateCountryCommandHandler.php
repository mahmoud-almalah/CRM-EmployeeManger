<?php

declare(strict_types=1);

namespace Application\Country\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\Country\Commands\CreateCountryCommand;
use Application\Country\Contracts\CountryServiceContract;
use Domain\Country\Entities\Country;
use InvalidArgumentException;

final class CreateCountryCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly CountryServiceContract $countries,
    ) {}

    public function handle(Command $command): Country
    {
        if (! $command instanceof CreateCountryCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', CreateCountryCommand::class, $command::class));
        }

        return $this->countries->create($command->data);
    }
}
