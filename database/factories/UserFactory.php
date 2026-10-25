<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\User\Entities\User;
use Domain\User\ValueObjects\EmailObject;
use Domain\User\ValueObjects\NameObject;
use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Shared\Enums\CurrencyEnum;
use Shared\Enums\UserStatus;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => new NameObject(fake()->firstName(), null, fake()->lastName()),
            'username' => fake()->unique()->userName(),
            'email' => new EmailObject(fake()->unique()->safeEmail()),
            'phone_number' => new PhoneObject((string) fake()->unique()->numerify('1#########'), '+20'),
            'address' => fake()->streetAddress(),
            'birth_date' => fake()->dateTimeBetween('-50 years', '-21 years'),
            'start_working_date' => fake()->dateTimeBetween('-5 years', '-1 month'),
            'salary' => fake()->randomFloat(2, 800, 6000),
            'salary_currency' => CurrencyEnum::USD,
            'is_freelance' => false,
            'is_remote' => false,
            'status' => UserStatus::Active,
            'password' => 'password',
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => UserStatus::Inactive]);
    }

    public function terminated(): static
    {
        return $this->state(fn () => [
            'status' => UserStatus::Terminated,
            'end_working_date' => fake()->dateTimeBetween('-1 month', 'now'),
            'termination_reason' => fake()->sentence(),
        ]);
    }

    public function remote(): static
    {
        return $this->state(['is_remote' => true]);
    }

    public function freelance(): static
    {
        return $this->state(['is_freelance' => true]);
    }
}
