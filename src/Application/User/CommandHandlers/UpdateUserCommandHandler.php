<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\User\Commands\UpdateUserCommand;
use Application\User\Contracts\UserServiceContract;
use Domain\User\Entities\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class UpdateUserCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly UserServiceContract $users,
    ) {}

    public function handle(Command $command): User
    {
        if (! $command instanceof UpdateUserCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', UpdateUserCommand::class, $command::class));
        }

        return DB::transaction(function () use ($command): User {
            $override = $command->data->password === null ? [] : ['password_changed_at' => now()];

            $user = $this->users->update($command->userId, $command->data, $override);

            if ($command->role !== null) {
                $user->syncRoles([$command->role->value]);
            }

            return $user;
        });
    }
}
