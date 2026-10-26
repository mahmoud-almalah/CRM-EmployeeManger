<?php

declare(strict_types=1);

namespace Application\City\Data;

use Application\Data\DataEntity;

final class CityData extends DataEntity
{
    public function __construct(
        public readonly int $countryId,
        public readonly string $name,
        public readonly ?string $code = null,
        public readonly ?float $latitude = null,
        public readonly ?float $longitude = null,
        public readonly ?string $timezone = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            countryId: (int) $data['country_id'],
            name: $data['name'],
            code: $data['code'] ?? null,
            latitude: isset($data['coordinates']['lat']) ? (float) $data['coordinates']['lat'] : null,
            longitude: isset($data['coordinates']['lng']) ? (float) $data['coordinates']['lng'] : null,
            timezone: $data['timezone'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'country_id' => $this->countryId,
            'name' => $this->name,
            'code' => $this->code,
            'coordinates' => $this->latitude === null || $this->longitude === null
                ? null
                : ['lat' => $this->latitude, 'lng' => $this->longitude],
            'timezone' => $this->timezone,
        ];
    }
}
