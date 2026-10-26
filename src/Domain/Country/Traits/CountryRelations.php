<?php

declare(strict_types=1);

namespace Domain\Country\Traits;

use Domain\City\Entities\City;
use Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CountryRelations
{
    /**
     * @return HasMany<City, $this>
     */
    public function cities(): HasMany
    {
        return $this->hasMany(City::class);
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
