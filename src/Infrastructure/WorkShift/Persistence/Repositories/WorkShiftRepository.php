<?php

declare(strict_types=1);

namespace Infrastructure\WorkShift\Persistence\Repositories;

use Application\Repository\Repository;
use Application\WorkShift\Contracts\WorkShiftRepositoryContract;
use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WorkShiftRepository extends Repository implements WorkShiftRepositoryContract
{
    public function __construct()
    {
        parent::__construct(new WorkShift);
    }

    public function findInWorkArea(string $workAreaId, string $id): WorkShift
    {
        return $this->query()->where('work_area_id', $workAreaId)->findOrFail($id);
    }

    public function siblings(string $workAreaId, ?string $exceptId = null): Collection
    {
        return $this->query()
            ->where('work_area_id', $workAreaId)
            ->when($exceptId !== null, fn ($query) => $query->whereKeyNot($exceptId))
            ->get();
    }

    public function isInUse(string $id): bool
    {
        return DB::table('users')->where('work_shift_id', $id)->exists();
    }
}
