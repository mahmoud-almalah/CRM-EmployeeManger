<?php

declare(strict_types=1);

namespace Application\Supervisor\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\Supervisor\Commands\UpdateSupervisorCommand;
use Application\Supervisor\Contracts\SupervisorServiceContract;
use Domain\Supervisor\Entities\Supervisor;
use InvalidArgumentException;

final class UpdateSupervisorCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly SupervisorServiceContract $supervisors,
    ) {}

    public function handle(Command $command): Supervisor
    {
        if (! $command instanceof UpdateSupervisorCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', UpdateSupervisorCommand::class, $command::class));
        }

        return $this->supervisors->update($command->supervisorId, $command->data);
    }
}
