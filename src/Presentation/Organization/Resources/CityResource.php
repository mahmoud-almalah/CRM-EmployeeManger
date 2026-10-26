<?php

declare(strict_types=1);

namespace Presentation\Organization\Resources;

use Domain\City\Entities\City;
use Illuminate\Http\Request;
use Presentation\Resources\ApiResource;

/**
 * @mixin City
 */
final class CityResource extends ApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'country_id' => $this->country_id,
            'name' => $this->name,
            'code' => $this->code,
            'coordinates' => $this->coordinates,
            'timezone' => $this->timezone,
            'country' => CountryResource::make($this->whenLoaded('country')),
        ] + $this->timestamps();
    }
}
