<?php

declare(strict_types=1);

namespace Application\Auth\CommandHandlers;

use Application\Auth\Commands\LogoutCommand;
use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use InvalidArgumentException;
use Laravel\Passport\AccessToken;

final class LogoutCommandHandler extends CommandHandler
{
    public function handle(Command $command): mixed
    {
        if (! $command instanceof LogoutCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', LogoutCommand::class, $command::class));
        }

        $token = $command->user->token();

        if ($token instanceof AccessToken) {
            $token->revoke();
        }

        return null;
    }
}
