<?php

declare(strict_types=1);

namespace Application\User\CommandHandlers;

use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\User\Commands\DeleteUserCommand;
use Application\User\Contracts\UserServiceContract;
use Application\User\Exceptions\CannotDeleteOwnAccountException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final class DeleteUserCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly UserServiceContract $users,
    ) {}

    public function handle(Command $command): mixed
    {
        if (! $command instanceof DeleteUserCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', DeleteUserCommand::class, $command::class));
        }

        if ($command->userId === $command->requestedBy) {
            throw new CannotDeleteOwnAccountException;
        }

        DB::transaction(function () use ($command): void {
            $user = $this->users->find($command->userId);

            $user->tokens()->update(['revoked' => true]);
            $this->users->delete($command->userId);
        });

        return null;
    }
}
