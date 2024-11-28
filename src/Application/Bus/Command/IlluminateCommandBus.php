<?php

declare(strict_types=1);

namespace Application\Bus\Command;

use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Trait\BusMappingTrait;
use Application\Bus\Trait\LoggingTrait;
use Illuminate\Bus\Dispatcher;

class IlluminateCommandBus implements CommandBusContract
{
    use BusMappingTrait, LoggingTrait;

    public function dispatch(Command $command): mixed
    {
        $handler = $this->resolveHandler(get_class($command));

        if ($handler instanceof CommandHandler) {
            $this->logExecution('Command', $command);
            return $handler->handle($command);
        }

        throw new \InvalidArgumentException("Handler is not a valid CommandHandler instance.");
    }
}
