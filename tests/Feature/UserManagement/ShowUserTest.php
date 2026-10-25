<?php

use Domain\User\Entities\User;
use Illuminate\Support\Facades\DB;
use Shared\Enums\RoleName;

beforeEach(function () {
    actingAsRole(RoleName::Hr);
});

it('shows one user', function () {
    $user = User::factory()->create(['username' => 'sara']);

    $this->getJson("/api/users/{$user->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.username', 'sara')
        ->assertJsonMissingPath('data.roles');
});

it('includes roles on request', function () {
    $user = User::factory()->create()->assignRole('manager');

    $this->getJson("/api/users/{$user->id}?include=roles")
        ->assertOk()
        ->assertJsonPath('data.roles', ['manager']);
});

it('rejects an include that is not allowed', function () {
    $user = User::factory()->create();

    $this->getJson("/api/users/{$user->id}?include=tokens")->assertStatus(400);
});

it('answers 404 for an unknown or deleted user', function () {
    $deleted = User::factory()->create();
    $deleted->delete();

    $this->getJson('/api/users/'.fake()->uuid())->assertNotFound();
    $this->getJson("/api/users/{$deleted->id}")->assertNotFound();
});

it('loads the roles of a user with a fixed number of queries', function () {
    $user = User::factory()->create()->assignRole('employee', 'manager');
    $this->getJson("/api/users/{$user->id}?include=roles")->assertOk();

    $queries = 0;
    DB::listen(function () use (&$queries) {
        $queries++;
    });

    $this->getJson("/api/users/{$user->id}?include=roles")->assertOk();

    expect($queries)->toBeLessThanOrEqual(6);
});

it('requires the users.view permission', function () {
    $user = User::factory()->create();
    Laravel\Passport\Passport::actingAs(User::factory()->create(), [], 'api');

    $this->getJson("/api/users/{$user->id}")->assertForbidden();
});
