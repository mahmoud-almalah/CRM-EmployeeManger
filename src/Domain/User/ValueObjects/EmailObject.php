<?php

declare(strict_types=1);

namespace Domain\User\ValueObjects;

use InvalidArgumentException;
use Stringable;

final readonly class EmailObject implements Stringable
{
    public string $email;

    public function __construct(string $email)
    {
        $normalized = strtolower(trim($email));

        if (filter_var($normalized, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException("[$email] is not a valid email address.");
        }

        $this->email = $normalized;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function equals(self $other): bool
    {
        return $this->email === $other->email;
    }

    public function __toString(): string
    {
        return $this->email;
    }
}
