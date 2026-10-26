<?php

declare(strict_types=1);

namespace Application\WorkShift\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\WorkShift\Commands\DeleteWorkShiftCommand;
use Application\WorkShift\Contracts\WorkShiftServiceContract;
use Application\WorkShift\Exceptions\WorkShiftInUseException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class DeleteWorkShiftCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly WorkShiftServiceContract $shifts,
    ) {}

    public function handle(Command $command): mixed
    {
        if (! $command instanceof DeleteWorkShiftCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', DeleteWorkShiftCommand::class, $command::class));
        }

        DB::transaction(function () use ($command): void {
            $shift = $this->shifts->findInWorkArea($command->workAreaId, $command->shiftId);

            if ($this->shifts->isInUse((string) $shift->id)) {
                throw new WorkShiftInUseException;
            }

            $this->shifts->delete((string) $shift->id);
        });

        return null;
    }
}
