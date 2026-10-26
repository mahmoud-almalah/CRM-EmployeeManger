<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\City\Entities\City;
use Domain\User\Entities\User;
use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Database\Eloquent\Factories\Factory;
use Shared\Enums\WorkAreaStatus;

/**
 * @extends Factory<WorkArea>
 */
class WorkAreaFactory extends Factory
{
    protected $model = WorkArea::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'address' => fake()->streetAddress(),
            'city_id' => CityFactory::new(),
            'country_id' => fn (array $attributes) => City::query()->findOrFail($attributes['city_id'])->country_id,
            'manager_id' => User::factory(),
            'phone' => fake()->numerify('+20 1#########'),
            'email' => fake()->unique()->safeEmail(),
            'status' => WorkAreaStatus::Open,
            'opening_hours' => '09:00-17:00',
            'description' => fake()->sentence(),
        ];
    }

    public function closed(): static
    {
        return $this->state(['status' => WorkAreaStatus::Closed]);
    }

    public function underMaintenance(): static
    {
        return $this->state(['status' => WorkAreaStatus::UnderMaintenance]);
    }
}
