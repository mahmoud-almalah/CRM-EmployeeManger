<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Requests;

use Application\User\Data\UserData;
use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Shared\Enums\CurrencyEnum;
use Shared\Enums\RoleName;
use Shared\Enums\UserStatus;

class CreateUserRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name.first' => ['required', 'string', 'max:100'],
            'name.middle' => ['nullable', 'string', 'max:100'],
            'name.last' => ['nullable', 'string', 'max:100'],
            'username' => ['required', 'string', 'regex:/^[A-Za-z0-9._-]+$/', 'max:50', Rule::unique('users', 'username')],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')],
            'phone.country_code' => ['required', 'regex:/^\+?\d{1,4}$/'],
            'phone.number' => ['required', 'digits_between:6,15'],
            'address' => ['nullable', 'string', 'max:255'],
            'country_id' => ['required', 'integer', Rule::exists('countries', 'id')],
            'city_id' => ['required', 'integer', Rule::exists('cities', 'id')->where('country_id', $this->input('country_id'))],
            'position_id' => ['required', 'integer', Rule::exists('positions', 'id')],
            'work_area_id' => ['required', 'integer', Rule::exists('work_areas', 'id')],
            'work_shift_id' => ['required', 'integer', Rule::exists('work_shifts', 'id')->where('work_area_id', $this->input('work_area_id'))],
            'birth_date' => ['required', 'date', 'before:today'],
            'start_working_date' => ['required', 'date'],
            'end_working_date' => ['nullable', 'date', 'after_or_equal:start_working_date'],
            'termination_reason' => ['nullable', 'string', 'max:500'],
            'salary' => ['required', 'numeric', 'min:0'],
            'salary_currency' => ['nullable', Rule::enum(CurrencyEnum::class)],
            'is_freelance' => ['nullable', 'boolean'],
            'is_remote' => ['nullable', 'boolean'],
            'status' => ['nullable', Rule::enum(UserStatus::class)],
            'role' => ['required', Rule::enum(RoleName::class)],
            'password' => ['required', 'string', 'min:8'],
            'additional_info' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'city_id.exists' => 'The selected city does not belong to the selected country.',
            'work_shift_id.exists' => 'The selected shift does not belong to the selected work area.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => strtolower(trim($this->input('email')))]);
        }
    }

    /**
     * The phone is stored as one unique string, so uniqueness is checked on the normalized value.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->hasAny(['phone.country_code', 'phone.number'])) {
                    return;
                }

                try {
                    $phone = new PhoneObject((string) $this->input('phone.number'), (string) $this->input('phone.country_code'));
                } catch (InvalidArgumentException) {
                    return;
                }

                $taken = DB::table('users')
                    ->where('phone_number', (string) $phone)
                    ->when($this->route('id'), fn ($query, $id) => $query->where('id', '!=', $id))
                    ->exists();

                if ($taken) {
                    $validator->errors()->add('phone.number', 'The phone number has already been taken.');
                }
            },
        ];
    }

    public function toData(): UserData
    {
        return UserData::fromArray($this->validated());
    }

    public function role(): RoleName
    {
        return RoleName::from($this->validated('role'));
    }
}
