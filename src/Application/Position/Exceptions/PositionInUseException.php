<?php

declare(strict_types=1);

namespace Application\Position\Exceptions;

use Application\Exceptions\BusinessConflictException;

final class PositionInUseException extends BusinessConflictException
{
    public function __construct()
    {
        parent::__construct('The position is still held by users and cannot be deleted.');
    }
}
