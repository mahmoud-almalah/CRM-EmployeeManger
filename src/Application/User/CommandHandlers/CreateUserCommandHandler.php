<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\User\Commands\CreateUserCommand;
use Application\User\Contracts\UserServiceContract;
use Domain\User\Entities\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class CreateUserCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly UserServiceContract $users,
    ) {}

    public function handle(Command $command): User
    {
        if (! $command instanceof CreateUserCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', CreateUserCommand::class, $command::class));
        }

        return DB::transaction(function () use ($command): User {
            $user = $this->users->create($command->data, ['password_changed_at' => now()]);
            $user->assignRole($command->role->value);

            return $user;
        });
    }
}
