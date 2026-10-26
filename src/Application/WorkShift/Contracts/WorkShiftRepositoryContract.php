<?php

declare(strict_types=1);

namespace Application\WorkShift\Contracts;

use Application\Repository\RepositoryInterface;
use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Database\Eloquent\Collection;

interface WorkShiftRepositoryContract extends RepositoryInterface
{
    /**
     * A shift of the given work area, or a ModelNotFoundException when it
     * belongs to another one.
     */
    public function findInWorkArea(string $workAreaId, string $id): WorkShift;

    /**
     * The other shifts of a work area, optionally leaving one out.
     *
     * @return Collection<int, WorkShift>
     */
    public function siblings(string $workAreaId, ?string $exceptId = null): Collection;

    /**
     * Whether any user is still assigned to the shift.
     */
    public function isInUse(string $id): bool;
}
