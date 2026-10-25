<?php

declare(strict_types=1);

namespace Application\Auth\CommandHandlers;

use Application\Auth\Commands\LogoutCommand;
use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use InvalidArgumentException;

final class LogoutCommandHandler extends CommandHandler
{
    public function handle(Command $command): mixed
    {
        if (! $command instanceof LogoutCommand) {
            throw new InvalidArgumentException(sprintf('Expected %s, got %s.', LogoutCommand::class, $command::class));
        }

        $command->user->token()?->revoke();

        return null;
    }
}
