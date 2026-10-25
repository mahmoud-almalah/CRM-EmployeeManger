<?php

declare(strict_types=1);

namespace Domain\User\Casts;

use Domain\User\ValueObjects\EmailObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Stored as the plain address so the unique index and lookups keep working.
 *
 * @implements CastsAttributes<EmailObject, mixed>
 */
final class Email implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): ?EmailObject
    {
        return $value === null ? null : new EmailObject((string) $value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        return match (true) {
            $value === null => null,
            is_string($value) => (string) new EmailObject($value),
            $value instanceof EmailObject => (string) $value,
            default => throw new InvalidArgumentException('Value must be an EmailObject or a string.'),
        };
    }
}
