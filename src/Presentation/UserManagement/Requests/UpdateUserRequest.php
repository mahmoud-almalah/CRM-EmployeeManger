<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Requests;

use Domain\User\Entities\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Shared\Enums\RoleName;

final class UpdateUserRequest extends CreateUserRequest
{
    /**
     * Same rules as creating, except the account being edited may keep its own
     * username and email, and the password and role are only changed when sent.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return array_merge(parent::rules(), [
            'username' => ['required', 'string', 'regex:/^[A-Za-z0-9._-]+$/', 'max:50', Rule::unique('users', 'username')->ignore($id)],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['nullable', Rule::enum(RoleName::class)],
        ]);
    }

    /**
     * A missing user is a 404, whatever else is wrong with the payload.
     */
    protected function prepareForValidation(): void
    {
        if (! User::query()->whereKey($this->route('id'))->exists()) {
            throw (new ModelNotFoundException)->setModel(User::class, [(string) $this->route('id')]);
        }

        parent::prepareForValidation();
    }

    public function newRole(): ?RoleName
    {
        $role = $this->validated('role');

        return $role === null ? null : RoleName::from($role);
    }
}
