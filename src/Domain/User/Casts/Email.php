<?php

namespace Domain\User\Casts;

use Application\User\ValueObjects\EmailObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class Email implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): EmailObject
    {
        $email = json_decode($value, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);

        return new EmailObject(
            email: $email['email'],
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (!$value instanceof EmailObject) {
            throw new InvalidArgumentException(
                message: 'Value must be an instance of Email Object.',
            );
        }

        return json_encode(
            value: [
                $key => $value,
            ],
            flags: JSON_THROW_ON_ERROR,
        );
    }
}
