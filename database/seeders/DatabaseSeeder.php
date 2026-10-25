<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\User\Entities\User;
use Domain\User\ValueObjects\EmailObject;
use Domain\User\ValueObjects\NameObject;
use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Database\Seeder;
use Shared\Enums\RoleName;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        User::factory()->create([
            'name' => new NameObject('System', null, 'Admin'),
            'username' => 'admin',
            'email' => new EmailObject('admin@example.com'),
            'phone_number' => new PhoneObject('1000000000', '+20'),
            'password' => 'password',
        ])->assignRole(RoleName::Admin->value);

        User::factory()->create(['username' => 'hr'])->assignRole(RoleName::Hr->value);
        User::factory()->create(['username' => 'manager'])->assignRole(RoleName::Manager->value);

        User::factory()->count(6)->create()->each->assignRole(RoleName::Employee->value);
        User::factory()->count(2)->remote()->create()->each->assignRole(RoleName::Employee->value);
        User::factory()->freelance()->create()->assignRole(RoleName::Employee->value);
        User::factory()->inactive()->create()->assignRole(RoleName::Employee->value);
        User::factory()->terminated()->create()->assignRole(RoleName::Employee->value);
    }
}
