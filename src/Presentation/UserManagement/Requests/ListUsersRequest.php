<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Requests;

use Application\User\Queries\GetUsersQuery;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Shared\Enums\UserStatus;

final class ListUsersRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'filter' => ['nullable', 'array'],
            'filter.status' => ['nullable', Rule::enum(UserStatus::class)],
            'filter.position_id' => ['nullable', 'integer'],
            'filter.work_area_id' => ['nullable', 'integer'],
            'filter.is_remote' => ['nullable', 'in:true,false,1,0'],
            'filter.is_freelance' => ['nullable', 'in:true,false,1,0'],
            'filter.search' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'string', 'max:100'],
            'include' => ['nullable', 'string', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function toQuery(): GetUsersQuery
    {
        $include = $this->validated('include');

        return new GetUsersQuery(
            filters: (array) $this->input('filter', []),
            sort: $this->validated('sort'),
            include: $include === null ? [] : explode(',', $include),
            page: (int) ($this->validated('page') ?? 1),
            perPage: (int) ($this->validated('per_page') ?? 15),
        );
    }
}
