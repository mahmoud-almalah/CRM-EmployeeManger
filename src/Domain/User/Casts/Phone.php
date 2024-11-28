<?php

namespace Domain\User\Casts;

use Application\User\ValueObjects\PhoneObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class Phone implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): PhoneObject
    {
        $phone = json_decode($value, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);

        return new PhoneObject(
            number: $phone['number'],
            countryCode: $phone['countryCode'],
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (!$value instanceof PhoneObject) {
            throw new InvalidArgumentException(
                message: 'Value must be an instance of Phone Object',
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
