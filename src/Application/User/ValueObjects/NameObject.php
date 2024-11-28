<?php

namespace Application\User\ValueObjects;

use function json_encode;

final readonly class NameObject
{
    public function __construct(
        public string $first,
        public ?string $middle,
        public ?string $last,
    ) {
    }

    public function __toArray(): array
    {
        return [
            'first' => $this->first,
            'middle' => $this->middle,
            'last' => $this->last,
        ];
    }

    public function fullName(): string
    {
        return implode(
            ' ',
            array_filter(
                [
                    $this->first,
                    $this->middle,
                    $this->last,
                ],
            ),
        );
    }

    public function __toString(): string
    {
        return json_encode(
            value: ['name' => $this->__toArray()],
            flags: JSON_THROW_ON_ERROR,
        );
    }
}
