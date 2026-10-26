<?php

declare(strict_types=1);

namespace Application\WorkShift\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\WorkArea\Contracts\WorkAreaServiceContract;
use Application\WorkShift\Commands\CreateWorkShiftCommand;
use Application\WorkShift\Contracts\WorkShiftServiceContract;
use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class CreateWorkShiftCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly WorkShiftServiceContract $shifts,
        private readonly WorkAreaServiceContract $workAreas,
    ) {}

    public function handle(Command $command): WorkShift
    {
        if (! $command instanceof CreateWorkShiftCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', CreateWorkShiftCommand::class, $command::class));
        }

        return DB::transaction(function () use ($command): WorkShift {
            $this->workAreas->find((string) $command->data->workAreaId);
            $this->shifts->assertNoOverlap($command->data);

            return $this->shifts->create($command->data);
        });
    }
}
