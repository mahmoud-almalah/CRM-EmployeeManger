<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Database\Eloquent\Factories\Factory;
use Shared\Enums\ShiftName;

/**
 * @extends Factory<WorkShift>
 */
class WorkShiftFactory extends Factory
{
    protected $model = WorkShift::class;

    public function definition(): array
    {
        return [
            'work_area_id' => WorkAreaFactory::new(),
            'name' => ShiftName::Morning,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'description' => fake()->sentence(),
        ];
    }

    public function morning(): static
    {
        return $this->state(['name' => ShiftName::Morning, 'start_time' => '06:00', 'end_time' => '14:00']);
    }

    public function afternoon(): static
    {
        return $this->state(['name' => ShiftName::Afternoon, 'start_time' => '14:00', 'end_time' => '22:00']);
    }

    public function night(): static
    {
        return $this->state(['name' => ShiftName::Night, 'start_time' => '22:00', 'end_time' => '06:00']);
    }
}
