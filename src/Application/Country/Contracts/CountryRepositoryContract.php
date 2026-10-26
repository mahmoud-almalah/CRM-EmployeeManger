<?php

declare(strict_types=1);

namespace Application\Country\Contracts;

use Application\Repository\RepositoryInterface;

interface CountryRepositoryContract extends RepositoryInterface
{
    /**
     * Whether any other row still points to the country.
     */
    public function isInUse(string $id): bool;
}
