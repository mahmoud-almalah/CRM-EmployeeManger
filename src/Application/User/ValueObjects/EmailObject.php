<?php

namespace Application\User\ValueObjects;

use function json_encode;

final readonly class EmailObject
{
    public function __construct(
        public string $email,
    ) {}

    public function __toArray(): array
    {
        return [
            'email' => $this->email,
        ];
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isEqual(EmailObject $email): bool
    {
        return $this->email === $email->email;
    }

    public function __toString(): string
    {
        return json_encode(
            value: ['email' => $this->__toArray()],
            flags: JSON_THROW_ON_ERROR,
        );
    }
}
