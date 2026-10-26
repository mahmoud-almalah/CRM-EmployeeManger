<?php

declare(strict_types=1);

namespace Presentation\Organization\Requests;

use Application\WorkShift\Data\WorkShiftData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Shared\Enums\ShiftName;

/**
 * Used for both create (POST) and update (PUT). The work area comes from the
 * route. An end time earlier than the start time means the shift runs past
 * midnight; the same time for both is rejected.
 */
final class WorkShiftRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', Rule::enum(ShiftName::class)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'different:start_time'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function toData(): WorkShiftData
    {
        return WorkShiftData::fromArray(
            $this->validated() + ['work_area_id' => $this->route('workAreaId')],
        );
    }
}
