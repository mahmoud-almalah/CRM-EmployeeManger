<?php

declare(strict_types=1);

namespace Application\Bus\Command;

abstract class CommandHandler
{
    abstract public function handle(Command $command): mixed;
}
