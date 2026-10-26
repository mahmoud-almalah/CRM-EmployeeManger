<?php

declare(strict_types=1);

namespace Application\WorkShift\Exceptions;

use Application\Exceptions\BusinessConflictException;

final class WorkShiftInUseException extends BusinessConflictException
{
    public function __construct()
    {
        parent::__construct('The shift still has employees assigned and cannot be deleted.');
    }
}
