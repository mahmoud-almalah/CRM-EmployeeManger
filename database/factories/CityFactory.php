<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\City\Entities\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        return [
            'country_id' => CountryFactory::new(),
            'name' => fake()->unique()->city(),
            'code' => strtoupper(fake()->lexify('???')),
            'coordinates' => ['lat' => (float) fake()->latitude(), 'lng' => (float) fake()->longitude()],
            'timezone' => fake()->randomElement(['Africa/Cairo', 'Europe/Berlin', 'Asia/Tokyo', 'America/New_York']),
        ];
    }
}
