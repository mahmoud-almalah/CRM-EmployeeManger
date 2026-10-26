<?php

declare(strict_types=1);

namespace Domain\City\Entities;

use Database\Factories\CityFactory;
use Domain\City\Traits\CityRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property string|null $code
 * @property array{lat: float, lng: float}|null $coordinates
 * @property string|null $timezone
 */
final class City extends Model
{
    use CityRelations, HasFactory;

    public const TABLE = 'cities';

    protected $table = self::TABLE;

    protected $fillable = ['country_id', 'name', 'code', 'coordinates', 'timezone'];

    protected static function newFactory(): CityFactory
    {
        return CityFactory::new();
    }

    protected function casts(): array
    {
        return ['coordinates' => 'array'];
    }
}
