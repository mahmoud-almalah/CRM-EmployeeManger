<?php

declare(strict_types=1);

namespace Presentation\UserManagement\Resources;

use Domain\User\Entities\User;
use Illuminate\Http\Request;
use Presentation\Organization\Resources\CityResource;
use Presentation\Organization\Resources\CountryResource;
use Presentation\Organization\Resources\PositionResource;
use Presentation\Organization\Resources\WorkAreaResource;
use Presentation\Organization\Resources\WorkShiftResource;
use Presentation\Resources\ApiResource;

/**
 * @mixin User
 */
final class UserResource extends ApiResource
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
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'position_id' => $this->position_id,
            'work_area_id' => $this->work_area_id,
            'work_shift_id' => $this->work_shift_id,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')->values()),
            'country' => CountryResource::make($this->whenLoaded('country')),
            'city' => CityResource::make($this->whenLoaded('city')),
            'position' => PositionResource::make($this->whenLoaded('position')),
            'work_area' => WorkAreaResource::make($this->whenLoaded('workArea')),
            'work_shift' => WorkShiftResource::make($this->whenLoaded('workShift')),
        ];
    }
}
