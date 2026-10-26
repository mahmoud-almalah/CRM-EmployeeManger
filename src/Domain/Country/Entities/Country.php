<?php

declare(strict_types=1);

namespace Domain\Country\Entities;

use Database\Factories\CountryFactory;
use Domain\Country\Traits\CountryRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Shared\Enums\CurrencyEnum;

/**
 * @property int $id
 * @property string $name
 * @property string $phone_code
 * @property CurrencyEnum|null $currency
 */
final class Country extends Model
{
    use CountryRelations, HasFactory;

    public const TABLE = 'countries';

    protected $table = self::TABLE;

    protected $fillable = ['name', 'phone_code', 'currency'];

    protected static function newFactory(): CountryFactory
    {
        return CountryFactory::new();
    }

    protected function casts(): array
    {
        return ['currency' => CurrencyEnum::class];
    }
}
