<?php

declare(strict_types=1);

namespace Presentation\Organization\Requests;

use Application\WorkShift\Queries\GetWorkShiftsQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Shared\Enums\ShiftName;

final class ListWorkShiftsRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'array'],
            'filter.name' => ['nullable', Rule::enum(ShiftName::class)],
            'sort' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toQuery(): GetWorkShiftsQuery
    {
        return new GetWorkShiftsQuery(
            workAreaId: (string) $this->route('workAreaId'),
            filters: (array) $this->input('filter', []),
            sort: $this->validated('sort'),
            page: (int) ($this->validated('page') ?? 1),
            perPage: (int) ($this->validated('per_page') ?? 15),
        );
    }
}
