<?php

declare(strict_types=1);

namespace Domain\User\Casts;

use Domain\User\ValueObjects\NameObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Stored as JSON: {"first": "...", "middle": "...", "last": "..."}.
 *
 * @implements CastsAttributes<NameObject, mixed>
 */
final class Name implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?NameObject
    {
        if ($value === null) {
            return null;
        }

        return NameObject::fromArray(json_decode((string) $value, true, 512, JSON_THROW_ON_ERROR));
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        $name = match (true) {
            $value === null => null,
            is_array($value) => NameObject::fromArray($value),
            $value instanceof NameObject => $value,
            default => throw new InvalidArgumentException('Value must be a NameObject or an array.'),
        };

        return $name === null ? null : json_encode($name->toArray(), JSON_THROW_ON_ERROR);
    }
}
