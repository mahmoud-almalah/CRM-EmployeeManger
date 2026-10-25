<?php

declare(strict_types=1);

namespace Application\Auth\CommandHandlers;

use Application\Auth\Commands\LoginCommand;
use Application\Auth\Data\LoginResult;
use Application\Auth\Exceptions\AccountNotActiveException;
use Application\Auth\Exceptions\InvalidCredentialsException;
use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\User\Contracts\UserRepositoryContract;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Shared\Enums\UserStatus;

final class LoginCommandHandler extends CommandHandler
{
    public function __construct(
        private readonly UserRepositoryContract $users,
    ) {}

    public function handle(Command $command): LoginResult
    {
        if (! $command instanceof LoginCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', LoginCommand::class, $command::class));
        }

        $user = $this->users->findByLogin($command->login);

        if ($user === null || ! Hash::check($command->password, $user->password)) {
            throw new InvalidCredentialsException;
        }

        if ($user->status !== UserStatus::Active) {
            throw new AccountNotActiveException;
        }

        return new LoginResult($user->createToken('api')->accessToken, $user);
    }
}
