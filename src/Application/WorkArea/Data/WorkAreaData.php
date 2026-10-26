<?php

declare(strict_types=1);

namespace Application\WorkArea\Data;

use Application\Data\DataEntity;
use Shared\Enums\WorkAreaStatus;

final class WorkAreaData extends DataEntity
{
    public function __construct(
        public readonly string $name,
        public readonly string $address,
        public readonly int $cityId,
        public readonly int $countryId,
        public readonly string $managerId,
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
        public readonly WorkAreaStatus $status = WorkAreaStatus::Open,
        public readonly ?string $openingHours = null,
        public readonly ?string $description = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            address: $data['address'],
            cityId: (int) $data['city_id'],
            countryId: (int) $data['country_id'],
            managerId: $data['manager_id'],
            phone: $data['phone'] ?? null,
            email: isset($data['email']) ? strtolower($data['email']) : null,
            status: isset($data['status']) ? WorkAreaStatus::from($data['status']) : WorkAreaStatus::Open,
            openingHours: $data['opening_hours'] ?? null,
            description: $data['description'] ?? null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'city_id' => $this->cityId,
            'country_id' => $this->countryId,
            'manager_id' => $this->managerId,
            'phone' => $this->phone,
            'email' => $this->email,
            'status' => $this->status,
            'opening_hours' => $this->openingHours,
            'description' => $this->description,
        ];
    }
}
