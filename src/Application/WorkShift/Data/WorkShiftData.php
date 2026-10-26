<?php

declare(strict_types=1);

namespace Application\WorkShift\Data;

use Application\Data\DataEntity;
use Domain\WorkShift\ValueObjects\ShiftWindow;
use Shared\Enums\ShiftName;

final class WorkShiftData extends DataEntity
{
    public function __construct(
        public readonly int $workAreaId,
        public readonly ShiftName $name,
        public readonly string $startTime,
        public readonly string $endTime,
        public readonly ?string $description = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            workAreaId: (int) $data['work_area_id'],
            name: ShiftName::from($data['name']),
            startTime: $data['start_time'],
            endTime: $data['end_time'],
            description: $data['description'] ?? null,
        );
    }

    public function window(): ShiftWindow
    {
        return ShiftWindow::fromTimes($this->startTime, $this->endTime);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'work_area_id' => $this->workAreaId,
            'name' => $this->name,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
            'description' => $this->description,
        ];
    }
}
