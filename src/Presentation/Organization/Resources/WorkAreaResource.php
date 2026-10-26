<?php

declare(strict_types=1);

namespace Presentation\Organization\Resources;

use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Http\Request;
use Presentation\Resources\ApiResource;
use Presentation\UserManagement\Resources\UserResource;

/**
 * @mixin WorkArea
 */
final class WorkAreaResource extends ApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city_id' => $this->city_id,
            'country_id' => $this->country_id,
            'manager_id' => $this->manager_id,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'opening_hours' => $this->opening_hours,
            'description' => $this->description,
            'city' => CityResource::make($this->whenLoaded('city')),
            'country' => CountryResource::make($this->whenLoaded('country')),
            'manager' => UserResource::make($this->whenLoaded('manager')),
        ] + $this->timestamps();
    }
}
