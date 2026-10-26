<?php

declare(strict_types=1);

namespace Presentation\Organization\Requests;

use Application\WorkArea\Queries\GetWorkAreasQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Shared\Enums\WorkAreaStatus;

final class ListWorkAreasRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'array'],
            'filter.name' => ['nullable', 'string', 'max:150'],
            'filter.city_id' => ['nullable', 'integer'],
            'filter.country_id' => ['nullable', 'integer'],
            'filter.status' => ['nullable', Rule::enum(WorkAreaStatus::class)],
            'sort' => ['nullable', 'string', 'max:100'],
            'include' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toQuery(): GetWorkAreasQuery
    {
        $include = $this->validated('include');

        return new GetWorkAreasQuery(
            filters: (array) $this->input('filter', []),
            sort: $this->validated('sort'),
            include: $include === null ? [] : explode(',', $include),
            page: (int) ($this->validated('page') ?? 1),
            perPage: (int) ($this->validated('per_page') ?? 15),
        );
    }
}
