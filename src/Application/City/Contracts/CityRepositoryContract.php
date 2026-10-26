<?php

declare(strict_types=1);

namespace Application\City\Contracts;

use Application\Repository\RepositoryInterface;

interface CityRepositoryContract extends RepositoryInterface
{
    /**
     * Whether any other row still points to the city.
     */
    public function isInUse(string $id): bool;
}
