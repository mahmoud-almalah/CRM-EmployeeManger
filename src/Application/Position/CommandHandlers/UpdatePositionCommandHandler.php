<?php

declare(strict_types=1);

namespace Application\Position\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\Position\Commands\UpdatePositionCommand;
use Application\Position\Contracts\PositionServiceContract;
use Domain\Position\Entities\Position;
use InvalidArgumentException;

final class UpdatePositionCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly PositionServiceContract $positions,
    ) {}

    public function handle(Command $command): Position
    {
        if (! $command instanceof UpdatePositionCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', UpdatePositionCommand::class, $command::class));
        }

        return $this->positions->update($command->positionId, $command->data);
    }
}
