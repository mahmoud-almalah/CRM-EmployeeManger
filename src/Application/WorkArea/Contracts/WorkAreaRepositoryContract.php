<?php

declare(strict_types=1);

namespace Application\WorkArea\Contracts;

use Application\Repository\RepositoryInterface;

interface WorkAreaRepositoryContract extends RepositoryInterface
{
    /**
     * Whether any other row still points to the workArea.
     */
    public function isInUse(string $id): bool;
}
