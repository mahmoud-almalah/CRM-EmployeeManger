<?php

declare(strict_types=1);

namespace Domain\Position\Entities;

use Database\Factories\PositionFactory;
use Domain\Position\Traits\PositionRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 */
final class Position extends Model
{
    use HasFactory, PositionRelations;

    public const TABLE = 'positions';

    protected $table = self::TABLE;

    protected $fillable = ['name', 'description'];

    protected static function newFactory(): PositionFactory
    {
        return PositionFactory::new();
    }
}
