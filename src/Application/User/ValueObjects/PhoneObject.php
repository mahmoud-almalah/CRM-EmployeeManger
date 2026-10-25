<?php

namespace Application\User\ValueObjects;

use Illuminate\Support\Str;

use function json_encode;

final readonly class PhoneObject
{
    public function __construct(
        public string $number,
        public string $countryCode,
    ) {}

    public function __toArray(): array
    {
        return [
            'number' => $this->number,
            'countryCode' => $this->countryCode,
        ];
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getFullNumber(): string
    {
        return "{$this->getCountryCode()}{$this->getNumber()}";
    }

    public function withPlus(): self
    {
        return new self(
            number: $this->number,
            countryCode: Str::of($this->countryCode)
                ->padLeft(1, '+')
                ->__toString(),
        );
    }

    public function getWhatsAppLink(): string
    {
        return Str::of('https://wa.me/')
            ->append($this->getFullNumber())
            ->__toString();
    }

    public function __toString(): string
    {
        return json_encode(
            value: ['phone' => $this->__toArray()],
            flags: JSON_THROW_ON_ERROR,
        );
    }
}
