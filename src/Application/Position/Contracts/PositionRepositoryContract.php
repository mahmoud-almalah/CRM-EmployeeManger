<?php

declare(strict_types=1);

namespace Application\Position\Contracts;

use Application\Repository\RepositoryInterface;

interface PositionRepositoryContract extends RepositoryInterface
{
    /**
     * Whether any other row still points to the position.
     */
    public function isInUse(string $id): bool;
}
