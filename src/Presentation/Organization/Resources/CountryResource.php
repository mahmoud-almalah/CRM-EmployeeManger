<?php

declare(strict_types=1);

namespace Presentation\Organization\Resources;

use Domain\Country\Entities\Country;
use Illuminate\Http\Request;
use Presentation\Resources\ApiResource;

/**
 * @mixin Country
 */
final class CountryResource extends ApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone_code' => $this->phone_code,
            'currency' => $this->currency,
        ] + $this->timestamps();
    }
}
