<?php

declare(strict_types=1);

namespace Application\Position\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\Position\Commands\CreatePositionCommand;
use Application\Position\Contracts\PositionServiceContract;
use Domain\Position\Entities\Position;
use InvalidArgumentException;

final class CreatePositionCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly PositionServiceContract $positions,
    ) {}

    public function handle(Command $command): Position
    {
        if (! $command instanceof CreatePositionCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', CreatePositionCommand::class, $command::class));
        }

        return $this->positions->create($command->data);
    }
}
