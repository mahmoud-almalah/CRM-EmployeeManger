<?php

declare(strict_types=1);

namespace Domain\User\ValueObjects;

use InvalidArgumentException;
use Stringable;

final readonly class PhoneObject implements Stringable
{
    public string $countryCode;

    public string $number;

    public function __construct(string $number, string $countryCode)
    {
        $code = preg_replace('/\D+/', '', $countryCode) ?? '';
        $digits = preg_replace('/\D+/', '', $number) ?? '';

        if ($code === '' || $digits === '') {
            throw new InvalidArgumentException('A phone number needs a country code and a number.');
        }

        $this->countryCode = '+'.$code;
        $this->number = $digits;
    }

    /**
     * Parses the stored form, "+20 1012345678".
     */
    public static function fromStorage(string $value): self
    {
        $parts = explode(' ', trim($value), 2);

        if (count($parts) !== 2) {
            throw new InvalidArgumentException("[$value] is not a stored phone number.");
        }

        return new self($parts[1], $parts[0]);
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
        return $this->countryCode.$this->number;
    }

    public function getWhatsAppLink(): string
    {
        return 'https://wa.me/'.ltrim($this->getFullNumber(), '+');
    }

    public function equals(self $other): bool
    {
        return $this->getFullNumber() === $other->getFullNumber();
    }

    public function __toString(): string
    {
        return $this->countryCode.' '.$this->number;
    }
}
