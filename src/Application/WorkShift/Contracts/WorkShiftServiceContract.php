<?php

declare(strict_types=1);

namespace Application\WorkShift\Contracts;

use Application\WorkShift\Data\WorkShiftData;
use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Database\Eloquent\Collection;

interface WorkShiftServiceContract
{
    public function findInWorkArea(string $workAreaId, string $id): WorkShift;

    public function create(WorkShiftData $data): WorkShift;

    public function update(string $id, WorkShiftData $data): WorkShift;

    public function delete(string $id): void;

    /**
     * Fails with a conflict when the window overlaps another shift of the same work area.
     */
    public function assertNoOverlap(WorkShiftData $data, ?string $exceptId = null): void;

    /**
     * @return Collection<int, WorkShift>
     */
    public function siblings(string $workAreaId, ?string $exceptId = null): Collection;

    public function isInUse(string $id): bool;
}
