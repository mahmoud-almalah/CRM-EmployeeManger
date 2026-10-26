<?php

declare(strict_types=1);

namespace Presentation\Organization\Resources;

use Domain\Supervisor\Entities\Supervisor;
use Illuminate\Http\Request;
use Presentation\Resources\ApiResource;

/**
 * @mixin Supervisor
 */
final class SupervisorResource extends ApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email->getEmail(),
            'phone' => [
                'country_code' => $this->phone_number->getCountryCode(),
                'number' => $this->phone_number->getNumber(),
            ],
            'status' => $this->status,
            'profile_picture' => $this->profile_picture,
        ] + $this->timestamps();
    }
}
