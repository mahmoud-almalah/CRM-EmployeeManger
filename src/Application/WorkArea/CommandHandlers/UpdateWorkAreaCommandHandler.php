<?php

declare(strict_types=1);

namespace Application\WorkArea\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\WorkArea\Commands\UpdateWorkAreaCommand;
use Application\WorkArea\Contracts\WorkAreaServiceContract;
use Domain\WorkArea\Entities\WorkArea;
use InvalidArgumentException;

final class UpdateWorkAreaCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly WorkAreaServiceContract $workAreas,
    ) {}

    public function handle(Command $command): WorkArea
    {
        if (! $command instanceof UpdateWorkAreaCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', UpdateWorkAreaCommand::class, $command::class));
        }

        return $this->workAreas->update($command->workAreaId, $command->data);
    }
}
