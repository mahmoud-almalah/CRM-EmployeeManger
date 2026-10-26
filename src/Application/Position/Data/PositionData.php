<?php

declare(strict_types=1);

namespace Application\Position\Data;

use Application\Data\DataEntity;

final class PositionData extends DataEntity
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
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
            'description' => $this->description,
        ];
    }
}
