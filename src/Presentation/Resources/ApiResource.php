<?php

declare(strict_types=1);

namespace Presentation\Resources;

use DateTimeInterface;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Base class for every API resource, so all modules answer with the same
 * envelope: a single item is {"data": {...}}, a collection is
 * {"data": [...], "links": {...}, "meta": {...}} when paginated.
 */
abstract class ApiResource extends JsonResource
{
    public static $wrap = 'data';

    /**
     * Dates are always sent as ISO 8601 strings, or null when missing.
     */
    protected function iso(?DateTimeInterface $date): ?string
    {
        return $date?->format(DATE_ATOM);
    }

    /**
     * The created_at / updated_at pair most resources end with.
     *
     * @return array{created_at: string|null, updated_at: string|null}
     */
    protected function timestamps(): array
    {
        return [
            'created_at' => $this->iso($this->resource->created_at),
            'updated_at' => $this->iso($this->resource->updated_at),
        ];
    }
}
