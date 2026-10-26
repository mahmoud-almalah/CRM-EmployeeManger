<?php

declare(strict_types=1);

namespace Application\City\Exceptions;

use Application\Exceptions\BusinessConflictException;

final class CityInUseException extends BusinessConflictException
{
    public function __construct()
    {
        parent::__construct('The city is still used by work areas or users and cannot be deleted.');
    }
}
