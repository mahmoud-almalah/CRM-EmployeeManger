<?php

declare(strict_types=1);

namespace Domain\User\Traits;

use Illuminate\Database\Eloquent\Builder;
use Shared\Enums\UserStatus;

trait UserScopes
{
    /**
     * Scope a query to only include active users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Active);
    }

    /**
     * Scope a query to only include inactive users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('active', UserStatus::Inactive);
    }

    /**
     * Scope a query to only include freelance users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeFreelance(Builder $query): Builder
    {
        return $query->where('is_freelance', true);
    }

    /**
     * Scope a query to only include remote users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRemote(Builder $query): Builder
    {
        return $query->where('is_remote', true);
    }

    /**
     * Scope a query to only include terminated users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeTerminated(Builder $query): Builder
    {
        return $query->where('status', UserStatus::Terminated)
            ->whereNotNull('end_working_date');
    }

    /**
     * Scope a query to only include not terminated users.
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeNotTerminated(Builder $query): Builder
    {
        return $query->whereNotIn('status', [UserStatus::Terminated])
            ->orWhereNull('end_working_date');
    }
}
