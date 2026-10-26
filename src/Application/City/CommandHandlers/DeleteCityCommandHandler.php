<?php

declare(strict_types=1);

namespace Application\City\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\City\Commands\DeleteCityCommand;
use Application\City\Contracts\CityServiceContract;
use Application\City\Exceptions\CityInUseException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class DeleteCityCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly CityServiceContract $cities,
    ) {}

    public function handle(Command $command): mixed
    {
        if (! $command instanceof DeleteCityCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', DeleteCityCommand::class, $command::class));
        }

        DB::transaction(function () use ($command): void {
            $this->cities->find($command->cityId);

            if ($this->cities->isInUse($command->cityId)) {
                throw new CityInUseException;
            }

            $this->cities->delete($command->cityId);
        });

        return null;
    }
}
