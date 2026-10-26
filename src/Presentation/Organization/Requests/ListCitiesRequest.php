<?php

declare(strict_types=1);

namespace Presentation\Organization\Requests;

use Application\City\Queries\GetCitiesQuery;
use Illuminate\Foundation\Http\FormRequest;

final class ListCitiesRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'array'],
            'filter.name' => ['nullable', 'string', 'max:100'],
            'filter.country_id' => ['nullable', 'integer'],
            'sort' => ['nullable', 'string', 'max:100'],
            'include' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toQuery(): GetCitiesQuery
    {
        $include = $this->validated('include');

        return new GetCitiesQuery(
            filters: (array) $this->input('filter', []),
            sort: $this->validated('sort'),
            include: $include === null ? [] : explode(',', $include),
            page: (int) ($this->validated('page') ?? 1),
            perPage: (int) ($this->validated('per_page') ?? 15),
        );
    }
}
