<?php

declare(strict_types=1);

namespace Application\User\Exceptions;

use RuntimeException;

final class CannotDeleteOwnAccountException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('You cannot delete your own account.');
    }
}
