<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Resources;

use Domain\User\Entities\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
final class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name->toArray() + ['full' => $this->name->fullName()],
            'username' => $this->username,
            'email' => $this->email->getEmail(),
            'phone' => [
                'country_code' => $this->phone_number->getCountryCode(),
                'number' => $this->phone_number->getNumber(),
            ],
            'status' => $this->status,
            'is_remote' => $this->is_remote,
            'is_freelance' => $this->is_freelance,
            'start_working_date' => $this->start_working_date->toDateString(),
            'end_working_date' => $this->end_working_date?->toDateString(),
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')->values()),
        ];
    }
}
