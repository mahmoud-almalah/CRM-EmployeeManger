<?php

namespace Shared\Enums;

use Shared\Traits\EnumValues;

enum CurrencyEnum: string
{
    use EnumValues;

    case USD = 'USD';
    case EUR = 'EUR';
    case JPY = 'JPY';
    case EGP = 'EGP';

    public function getSymbol(): string
    {
        return match ($this) {
            self::USD => '$',
            self::EUR => '€',
            self::JPY => '¥',
            self::EGP => '£',
        };
    }

    public function getCurrency(): string
    {
        return match ($this) {
            self::USD => 'United States Dollar',
            self::EUR => 'Euro',
            self::JPY => 'Japanese Yen',
            self::EGP => 'Egyptian Pound',
        };
    }

    public function getCountry(): string
    {
        return match ($this) {
            self::USD => 'United States',
            self::EUR => 'European Union',
            self::JPY => 'Japan',
            self::EGP => 'Egypt',
        };
    }

    public function getCountryCode(): string
    {
        return match ($this) {
            self::USD => 'US',
            self::EUR => 'EU',
            self::JPY => 'JP',
            self::EGP => 'EG',
        };
    }
}
