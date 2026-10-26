<?php

declare(strict_types=1);

namespace Presentation\Organization\Resources;

use Domain\Position\Entities\Position;
use Illuminate\Http\Request;
use Presentation\Resources\ApiResource;

/**
 * @mixin Position
 */
final class PositionResource extends ApiResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ] + $this->timestamps();
    }
}
