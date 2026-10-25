<?php

declare(strict_types=1);

namespace Domain\User\Casts;

use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Stored as "+<country code> <number>", for example "+20 1012345678".
 *
 * @implements CastsAttributes<PhoneObject, mixed>
 */
final class Phone implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?PhoneObject
    {
        return $value === null ? null : PhoneObject::fromStorage((string) $value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return match (true) {
            $value === null => null,
            is_string($value) => (string) PhoneObject::fromStorage($value),
            $value instanceof PhoneObject => (string) $value,
            default => throw new InvalidArgumentException('Value must be a PhoneObject or a stored string.'),
        };
    }
}
