<?php

declare(strict_types=1);

namespace Database\Factories;

use Domain\Supervisor\Entities\Supervisor;
use Domain\User\ValueObjects\EmailObject;
use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Database\Eloquent\Factories\Factory;
use Shared\Enums\SupervisorStatus;

/**
 * @extends Factory<Supervisor>
 */
class SupervisorFactory extends Factory
{
    protected $model = Supervisor::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => new EmailObject(fake()->unique()->safeEmail()),
            'phone_number' => new PhoneObject((string) fake()->unique()->numerify('1#########'), '+20'),
            'status' => SupervisorStatus::Active,
            'password' => 'password',
            'profile_picture' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['status' => SupervisorStatus::Inactive]);
    }
}
