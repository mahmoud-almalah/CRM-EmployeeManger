<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\Country\Entities\Country;
use Illuminate\Database\Eloquent\Factories\Factory;
use Shared\Enums\CurrencyEnum;

/**
 * @extends Factory<Country>
 */
class CountryFactory extends Factory
{
    protected $model = Country::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->country(),
            'phone_code' => '+'.fake()->numberBetween(1, 999),
            'currency' => fake()->randomElement(CurrencyEnum::cases()),
        ];
    }
}
