<?php

declare(strict_types=1);

namespace Application\City\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\City\Commands\UpdateCityCommand;
use Application\City\Contracts\CityServiceContract;
use Domain\City\Entities\City;
use InvalidArgumentException;

final class UpdateCityCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly CityServiceContract $cities,
    ) {}

    public function handle(Command $command): City
    {
        if (! $command instanceof UpdateCityCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', UpdateCityCommand::class, $command::class));
        }

        return $this->cities->update($command->cityId, $command->data);
    }
}
