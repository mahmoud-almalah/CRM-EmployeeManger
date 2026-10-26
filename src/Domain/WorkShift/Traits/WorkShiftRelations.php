<?php

declare(strict_types=1);

namespace Domain\WorkShift\Traits;

use Domain\User\Entities\User;
use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait WorkShiftRelations
{
    /**
     * @return BelongsTo<WorkArea, $this>
     */
    public function workArea(): BelongsTo
    {
        return $this->belongsTo(WorkArea::class);
    }

    /**
     * @return HasMany<User, $this>
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'work_shift_id');
    }
}
