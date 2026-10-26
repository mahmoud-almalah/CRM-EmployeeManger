<?php

declare(strict_types=1);

namespace Application\WorkArea\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\WorkArea\Commands\DeleteWorkAreaCommand;
use Application\WorkArea\Contracts\WorkAreaServiceContract;
use Application\WorkArea\Exceptions\WorkAreaInUseException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class DeleteWorkAreaCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly WorkAreaServiceContract $workAreas,
    ) {}

    public function handle(Command $command): mixed
    {
        if (! $command instanceof DeleteWorkAreaCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', DeleteWorkAreaCommand::class, $command::class));
        }

        DB::transaction(function () use ($command): void {
            $this->workAreas->find($command->workAreaId);

            if ($this->workAreas->isInUse($command->workAreaId)) {
                throw new WorkAreaInUseException;
            }

            $this->workAreas->delete($command->workAreaId);
        });

        return null;
    }
}
