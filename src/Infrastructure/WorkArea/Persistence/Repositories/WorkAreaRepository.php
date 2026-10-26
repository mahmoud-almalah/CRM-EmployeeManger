<?php

declare(strict_types=1);

namespace Infrastructure\WorkArea\Persistence\Repositories;

use Application\Repository\Repository;
use Application\WorkArea\Contracts\WorkAreaRepositoryContract;
use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Support\Facades\DB;

class WorkAreaRepository extends Repository implements WorkAreaRepositoryContract
{
    public function __construct()
    {
        parent::__construct(new WorkArea);
    }

    public function isInUse(string $id): bool
    {
        return DB::table('users')->where('work_area_id', $id)->exists();
    }
}
