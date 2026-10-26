<?php

declare(strict_types=1);

namespace Application\Supervisor\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\Supervisor\Commands\CreateSupervisorCommand;
use Application\Supervisor\Contracts\SupervisorServiceContract;
use Domain\Supervisor\Entities\Supervisor;
use InvalidArgumentException;

final class CreateSupervisorCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly SupervisorServiceContract $supervisors,
    ) {}

    public function handle(Command $command): Supervisor
    {
        if (! $command instanceof CreateSupervisorCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', CreateSupervisorCommand::class, $command::class));
        }

        return $this->supervisors->create($command->data);
    }
}
