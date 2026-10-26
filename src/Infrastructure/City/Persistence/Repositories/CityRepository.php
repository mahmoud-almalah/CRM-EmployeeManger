<?php

declare(strict_types=1);

namespace Infrastructure\City\Persistence\Repositories;

use Application\City\Contracts\CityRepositoryContract;
use Application\Repository\Repository;
use Domain\City\Entities\City;
use Illuminate\Support\Facades\DB;

class CityRepository extends Repository implements CityRepositoryContract
{
    public function __construct()
    {
        parent::__construct(new City);
    }

    public function isInUse(string $id): bool
    {
        return DB::table('work_areas')->where('city_id', $id)->exists()
            || DB::table('users')->where('city_id', $id)->exists();
    }
}
