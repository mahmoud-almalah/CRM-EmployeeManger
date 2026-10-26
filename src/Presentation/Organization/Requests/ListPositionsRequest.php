<?php

declare(strict_types=1);

namespace Presentation\Organization\Requests;

use Application\Position\Queries\GetPositionsQuery;
use Illuminate\Foundation\Http\FormRequest;

final class ListPositionsRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'array'],
            'filter.name' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toQuery(): GetPositionsQuery
    {
        return new GetPositionsQuery(
            filters: (array) $this->input('filter', []),
            sort: $this->validated('sort'),
            page: (int) ($this->validated('page') ?? 1),
            perPage: (int) ($this->validated('per_page') ?? 15),
        );
    }
}
