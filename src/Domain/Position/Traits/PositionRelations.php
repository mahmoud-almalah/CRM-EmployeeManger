<?php

declare(strict_types=1);

namespace Domain\Position\Traits;

use Domain\User\Entities\User;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait PositionRelations
{
    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
