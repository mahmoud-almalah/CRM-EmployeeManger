<?php

declare(strict_types=1);

namespace Application\Bus\Exceptions;

use RuntimeException;

final class HandlerNotFoundException extends RuntimeException
{
    public static function for(string $message): self
    {
        return new self("No handler registered for [$message].");
    }
}
