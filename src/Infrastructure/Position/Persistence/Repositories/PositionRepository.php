<?php

declare(strict_types=1);

namespace Infrastructure\Position\Persistence\Repositories;

use Application\Position\Contracts\PositionRepositoryContract;
use Application\Repository\Repository;
use Domain\Position\Entities\Position;
use Illuminate\Support\Facades\DB;

class PositionRepository extends Repository implements PositionRepositoryContract
{
    public function __construct()
    {
        parent::__construct(new Position);
    }

    public function isInUse(string $id): bool
    {
        return DB::table('users')->where('position_id', $id)->exists();
    }
}
