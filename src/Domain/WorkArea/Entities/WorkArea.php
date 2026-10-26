<?php

declare(strict_types=1);

namespace Domain\WorkArea\Entities;

use Database\Factories\WorkAreaFactory;
use Domain\WorkArea\Traits\WorkAreaRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Shared\Enums\WorkAreaStatus;

/**
 * @property int $id
 * @property string $name
 * @property string $address
 * @property int $city_id
 * @property int $country_id
 * @property string $manager_id
 * @property string|null $phone
 * @property string|null $email
 * @property WorkAreaStatus $status
 * @property string|null $opening_hours
 * @property string|null $description
 */
final class WorkArea extends Model
{
    use HasFactory, WorkAreaRelations;

    public const TABLE = 'work_areas';

    protected $table = self::TABLE;

    protected $fillable = [
        'name', 'address', 'city_id', 'country_id', 'manager_id',
        'phone', 'email', 'status', 'opening_hours', 'description',
    ];

    protected static function newFactory(): WorkAreaFactory
    {
        return WorkAreaFactory::new();
    }

    protected function casts(): array
    {
        return ['status' => WorkAreaStatus::class];
    }
}
