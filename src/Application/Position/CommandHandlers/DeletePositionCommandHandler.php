<?php

declare(strict_types=1);

namespace Application\Position\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\Position\Commands\DeletePositionCommand;
use Application\Position\Contracts\PositionServiceContract;
use Application\Position\Exceptions\PositionInUseException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class DeletePositionCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly PositionServiceContract $positions,
    ) {}

    public function handle(Command $command): mixed
    {
        if (! $command instanceof DeletePositionCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', DeletePositionCommand::class, $command::class));
        }

        DB::transaction(function () use ($command): void {
            $this->positions->find($command->positionId);

            if ($this->positions->isInUse($command->positionId)) {
                throw new PositionInUseException;
            }

            $this->positions->delete($command->positionId);
        });

        return null;
    }
}
