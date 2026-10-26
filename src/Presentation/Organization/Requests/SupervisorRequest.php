<?php

declare(strict_types=1);

namespace Presentation\Organization\Requests;

use Application\Supervisor\Data\SupervisorData;
use Domain\Supervisor\Entities\Supervisor;
use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Shared\Enums\SupervisorStatus;

/**
 * Used for both create (POST) and update (PUT). On update the password is
 * optional and the supervisor may keep their own email and phone.
 */
final class SupervisorRequest extends FormRequest
{
    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('supervisors', 'email')->ignore($id)],
            'phone.country_code' => ['required', 'regex:/^\+?\d{1,4}$/'],
            'phone.number' => ['required', 'digits_between:6,15'],
            'status' => ['nullable', Rule::enum(SupervisorStatus::class)],
            'password' => [$id === null ? 'required' : 'nullable', 'string', 'min:8'],
            'profile_picture' => ['nullable', 'string', 'max:2048'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->route('id') !== null && ! Supervisor::query()->whereKey($this->route('id'))->exists()) {
            throw (new ModelNotFoundException)->setModel(Supervisor::class, [(string) $this->route('id')]);
        }

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

                $taken = DB::table('supervisors')
                    ->where('phone_number', (string) $phone)
                    ->when($this->route('id'), fn ($query, $id) => $query->where('id', '!=', $id))
                    ->exists();

                if ($taken) {
                    $validator->errors()->add('phone.number', 'The phone number has already been taken.');
                }
            },
        ];
    }

    public function toData(): SupervisorData
    {
        return SupervisorData::fromArray($this->validated());
    }
}
