<?php

declare(strict_types=1);

namespace Database\Seeders;

use Domain\User\Entities\User;
use Domain\User\ValueObjects\EmailObject;
use Domain\User\ValueObjects\NameObject;
use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => new NameObject('System', null, 'Admin'),
            'username' => 'admin',
            'email' => new EmailObject('admin@example.com'),
            'phone_number' => new PhoneObject('1000000000', '+20'),
            'password' => 'password',
        ]);

        User::factory()->count(8)->create();
        User::factory()->count(2)->remote()->create();
        User::factory()->freelance()->create();
        User::factory()->inactive()->create();
        User::factory()->terminated()->create();
    }
}
