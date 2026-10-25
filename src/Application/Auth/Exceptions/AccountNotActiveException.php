<?php

declare(strict_types=1);

namespace Application\Auth\Exceptions;

use RuntimeException;

final class AccountNotActiveException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('This account is not active.');
    }
}
