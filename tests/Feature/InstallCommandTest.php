<?php

use Domain\User\Entities\User;
use Laravel\Passport\Client;
use Spatie\Permission\Models\Role;

it('prepares an empty environment and can be run again without duplicating anything', function () {
    $this->artisan('app:install')->assertSuccessful();

    $roles = Role::query()->count();
    $clients = Client::query()->where('personal_access_client', true)->count();

    expect($roles)->toBe(4)->and($clients)->toBe(1);

    $this->artisan('app:install')->assertSuccessful();

    expect(Role::query()->count())->toBe($roles)
        ->and(Client::query()->where('personal_access_client', true)->count())->toBe($clients);
});

it('does not create demo accounts unless asked to', function () {
    $this->artisan('app:install')->assertSuccessful();

    expect(User::query()->count())->toBe(0);
});
