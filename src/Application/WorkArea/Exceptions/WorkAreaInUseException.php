<?php

declare(strict_types=1);

namespace Application\WorkArea\Exceptions;

use Application\Exceptions\BusinessConflictException;

final class WorkAreaInUseException extends BusinessConflictException
{
    public function __construct()
    {
        parent::__construct('The work area still has employees assigned and cannot be deleted.');
    }
}
