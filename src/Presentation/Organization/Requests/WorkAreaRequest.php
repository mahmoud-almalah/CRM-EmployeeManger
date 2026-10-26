<?php

declare(strict_types=1);

namespace Presentation\Organization\Requests;

use Application\WorkArea\Data\WorkAreaData;
use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Shared\Enums\UserStatus;
use Shared\Enums\WorkAreaStatus;

/**
 * Used for both create (POST) and update (PUT).
 */
final class WorkAreaRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'integer', Rule::exists('countries', 'id')],
            'city_id' => [
                'required', 'integer',
                Rule::exists('cities', 'id')->where('country_id', $this->input('country_id')),
            ],
            'manager_id' => [
                'required', 'uuid',
                Rule::exists('users', 'id')->where('status', UserStatus::Active->value)->whereNull('deleted_at'),
            ],
            'phone' => ['nullable', 'string', 'regex:/^\+?[0-9 ()-]{6,30}$/'],
            'email' => ['nullable', 'email:rfc', 'max:255'],
            'status' => ['nullable', Rule::enum(WorkAreaStatus::class)],
            'opening_hours' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'city_id.exists' => 'The selected city does not belong to the selected country.',
            'manager_id.exists' => 'The manager must be an active user.',
        ];
    }

    /**
     * A missing work area is a 404, whatever else is wrong with the payload.
     */
    protected function prepareForValidation(): void
    {
        if ($this->route('id') !== null && ! WorkArea::query()->whereKey($this->route('id'))->exists()) {
            throw (new ModelNotFoundException)->setModel(WorkArea::class, [(string) $this->route('id')]);
        }
    }

    public function toData(): WorkAreaData
    {
        return WorkAreaData::fromArray($this->validated());
    }
}
