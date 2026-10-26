<?php

declare(strict_types=1);

namespace Application\Country\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\Country\Commands\DeleteCountryCommand;
use Application\Country\Contracts\CountryServiceContract;
use Application\Country\Exceptions\CountryInUseException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class DeleteCountryCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly CountryServiceContract $countries,
    ) {}

    public function handle(Command $command): mixed
    {
        if (! $command instanceof DeleteCountryCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', DeleteCountryCommand::class, $command::class));
        }

        DB::transaction(function () use ($command): void {
            $this->countries->find($command->countryId);

            if ($this->countries->isInUse($command->countryId)) {
                throw new CountryInUseException;
            }

            $this->countries->delete($command->countryId);
        });

        return null;
    }
}
