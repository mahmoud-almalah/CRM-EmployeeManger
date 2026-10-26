<?php

declare(strict_types=1);

namespace Domain\User\Traits;

use Domain\City\Entities\City;
use Domain\Country\Entities\Country;
use Domain\Position\Entities\Position;
use Domain\WorkArea\Entities\WorkArea;
use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait UserRelations
{
    /**
     * @return BelongsTo<Country, $this>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * @return BelongsTo<City, $this>
     */
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    /**
     * @return BelongsTo<Position, $this>
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * @return BelongsTo<WorkArea, $this>
     */
    public function workArea(): BelongsTo
    {
        return $this->belongsTo(WorkArea::class);
    }

    /**
     * @return BelongsTo<WorkShift, $this>
     */
    public function workShift(): BelongsTo
    {
        return $this->belongsTo(WorkShift::class);
    }
}
