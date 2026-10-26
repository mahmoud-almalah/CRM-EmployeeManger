<?php

declare(strict_types=1);

namespace Application\Country\Exceptions;

use Application\Exceptions\BusinessConflictException;

final class CountryInUseException extends BusinessConflictException
{
    public function __construct()
    {
        parent::__construct('The country is still used by cities, work areas or users and cannot be deleted.');
    }
}
