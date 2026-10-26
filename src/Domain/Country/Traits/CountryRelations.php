<?php

declare(strict_types=1);

namespace Domain\Country\Traits;

use Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CountryRelations
{
    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
