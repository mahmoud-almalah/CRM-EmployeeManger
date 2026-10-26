<?php

declare(strict_types=1);

namespace Infrastructure\Country\Persistence\Repositories;

use Application\Country\Contracts\CountryRepositoryContract;
use Application\Repository\Repository;
use Domain\Country\Entities\Country;
use Illuminate\Support\Facades\DB;

class CountryRepository extends Repository implements CountryRepositoryContract
{
    public function __construct()
    {
        parent::__construct(new Country);
    }

    public function isInUse(string $id): bool
    {
        return DB::table('cities')->where('country_id', $id)->exists()
            || DB::table('work_areas')->where('country_id', $id)->exists()
            || DB::table('users')->where('country_id', $id)->exists();
    }
}
