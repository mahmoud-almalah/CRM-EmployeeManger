<?php

declare(strict_types=1);

namespace Domain\WorkShift\Entities;

use Database\Factories\WorkShiftFactory;
use Domain\WorkShift\Traits\WorkShiftRelations;
use Domain\WorkShift\ValueObjects\ShiftWindow;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Shared\Enums\ShiftName;

/**
 * @property int $id
 * @property int $work_area_id
 * @property ShiftName $name
 * @property string $start_time
 * @property string $end_time
 * @property string|null $description
 */
final class WorkShift extends Model
{
    use HasFactory, WorkShiftRelations;

    public const TABLE = 'work_shifts';

    protected $table = self::TABLE;

    protected $fillable = ['work_area_id', 'name', 'start_time', 'end_time', 'description'];

    protected static function newFactory(): WorkShiftFactory
    {
        return WorkShiftFactory::new();
    }

    protected function casts(): array
    {
        return ['name' => ShiftName::class];
    }

    public function window(): ShiftWindow
    {
        return ShiftWindow::fromTimes($this->start_time, $this->end_time);
    }
}
