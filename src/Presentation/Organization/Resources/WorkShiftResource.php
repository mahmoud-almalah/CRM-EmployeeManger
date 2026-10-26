<?php

declare(strict_types=1);

namespace Presentation\Organization\Resources;

use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Http\Request;
use Presentation\Resources\ApiResource;

/**
 * @mixin WorkShift
 */
final class WorkShiftResource extends ApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'work_area_id' => $this->work_area_id,
            'name' => $this->name,
            'start_time' => substr($this->start_time, 0, 5),
            'end_time' => substr($this->end_time, 0, 5),
            'is_overnight' => $this->window()->isOvernight(),
            'description' => $this->description,
        ] + $this->timestamps();
    }
}
