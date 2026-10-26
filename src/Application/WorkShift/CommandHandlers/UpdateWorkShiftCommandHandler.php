<?php

declare(strict_types=1);

namespace Application\WorkShift\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\WorkShift\Commands\UpdateWorkShiftCommand;
use Application\WorkShift\Contracts\WorkShiftServiceContract;
use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class UpdateWorkShiftCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly WorkShiftServiceContract $shifts,
    ) {}

    public function handle(Command $command): WorkShift
    {
        if (! $command instanceof UpdateWorkShiftCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', UpdateWorkShiftCommand::class, $command::class));
        }

        return DB::transaction(function () use ($command): WorkShift {
            $shift = $this->shifts->findInWorkArea((string) $command->data->workAreaId, $command->shiftId);
            $this->shifts->assertNoOverlap($command->data, (string) $shift->id);

            return $this->shifts->update((string) $shift->id, $command->data);
        });
    }
}
