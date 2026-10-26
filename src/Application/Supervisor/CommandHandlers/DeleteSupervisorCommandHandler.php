<?php

declare(strict_types=1);

namespace Application\Supervisor\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\Supervisor\Commands\DeleteSupervisorCommand;
use Application\Supervisor\Contracts\SupervisorServiceContract;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class DeleteSupervisorCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly SupervisorServiceContract $supervisors,
    ) {}

    public function handle(Command $command): mixed
    {
        if (! $command instanceof DeleteSupervisorCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', DeleteSupervisorCommand::class, $command::class));
        }

        DB::transaction(function () use ($command): void {
            $this->supervisors->find($command->supervisorId);
            $this->supervisors->delete($command->supervisorId);
        });

        return null;
    }
}
