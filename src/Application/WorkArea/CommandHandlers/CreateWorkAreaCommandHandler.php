<?php

declare(strict_types=1);

namespace Application\WorkArea\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\WorkArea\Commands\CreateWorkAreaCommand;
use Application\WorkArea\Contracts\WorkAreaServiceContract;
use Domain\WorkArea\Entities\WorkArea;
use InvalidArgumentException;

final class CreateWorkAreaCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly WorkAreaServiceContract $workAreas,
    ) {}

    public function handle(Command $command): WorkArea
    {
        if (! $command instanceof CreateWorkAreaCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', CreateWorkAreaCommand::class, $command::class));
        }

        return $this->workAreas->create($command->data);
    }
}
