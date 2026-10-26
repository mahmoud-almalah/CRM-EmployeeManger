<?php

declare(strict_types=1);

namespace Application\WorkShift\Services;

use Application\WorkShift\Contracts\WorkShiftRepositoryContract;
use Application\WorkShift\Contracts\WorkShiftServiceContract;
use Application\WorkShift\Data\WorkShiftData;
use Application\WorkShift\Exceptions\ShiftOverlapException;
use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Database\Eloquent\Collection;

final class WorkShiftService implements WorkShiftServiceContract
{
    public function __construct(
        private readonly WorkShiftRepositoryContract $repository,
    ) {}

    public function findInWorkArea(string $workAreaId, string $id): WorkShift
    {
        return $this->repository->findInWorkArea($workAreaId, $id);
    }

    public function create(WorkShiftData $data): WorkShift
    {
        /** @var WorkShift */
        return $this->repository->create($data);
    }

    public function update(string $id, WorkShiftData $data): WorkShift
    {
        /** @var WorkShift */
        return $this->repository->update($id, $data);
    }

    public function delete(string $id): void
    {
        $this->repository->delete($id);
    }

    public function assertNoOverlap(WorkShiftData $data, ?string $exceptId = null): void
    {
        $window = $data->window();

        foreach ($this->repository->siblings((string) $data->workAreaId, $exceptId) as $other) {
            if ($window->overlaps($other->window())) {
                throw new ShiftOverlapException($other);
            }
        }
    }

    public function siblings(string $workAreaId, ?string $exceptId = null): Collection
    {
        return $this->repository->siblings($workAreaId, $exceptId);
    }

    public function isInUse(string $id): bool
    {
        return $this->repository->isInUse($id);
    }
}
