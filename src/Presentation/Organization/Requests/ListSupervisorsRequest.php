<?php

declare(strict_types=1);

namespace Presentation\Organization\Requests;

use Application\Supervisor\Queries\GetSupervisorsQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Shared\Enums\SupervisorStatus;

final class ListSupervisorsRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'array'],
            'filter.name' => ['nullable', 'string', 'max:150'],
            'filter.email' => ['nullable', 'string', 'max:255'],
            'filter.status' => ['nullable', Rule::enum(SupervisorStatus::class)],
            'sort' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toQuery(): GetSupervisorsQuery
    {
        return new GetSupervisorsQuery(
            filters: (array) $this->input('filter', []),
            sort: $this->validated('sort'),
            page: (int) ($this->validated('page') ?? 1),
            perPage: (int) ($this->validated('per_page') ?? 15),
        );
    }
}
