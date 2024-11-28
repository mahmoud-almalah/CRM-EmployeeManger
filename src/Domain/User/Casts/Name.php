<?php

namespace Domain\User\Casts;

use Application\User\ValueObjects\NameObject;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

final class Name implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): NameObject
    {
        $name = json_decode($value, true, JSON_THROW_ON_ERROR, JSON_THROW_ON_ERROR);

        return new NameObject(
            first: $name['name']['first'],
            middle: $name['name']['middle'],
            last: $name['name']['last'],
        );
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (!$value instanceof NameObject) {
            throw new InvalidArgumentException(
                message: 'Value must be an instance of Name Object',
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
