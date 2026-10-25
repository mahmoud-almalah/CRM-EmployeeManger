<?php

declare(strict_types=1);

namespace Domain\User\Traits;

use Illuminate\Database\Eloquent\Builder;
use Shared\Enums\UserStatus;

trait UserScopes
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Active);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Inactive);
    }

    public function scopeFreelance(Builder $query): Builder
    {
        return $query->where('is_freelance', true);
    }

    public function scopeRemote(Builder $query): Builder
    {
        return $query->where('is_remote', true);
    }

    public function scopeTerminated(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Terminated)
            ->whereNotNull('end_working_date');
    }

    /**
     * Anyone who has not been terminated. The condition is grouped so it can be
     * chained with other scopes without the OR escaping them.
     */
    public function scopeNotTerminated(Builder $query): Builder
    {
        return $query->where(
            fn (Builder $group) => $group->where('status', '!=', UserStatus::Terminated)
        );
    }
}
