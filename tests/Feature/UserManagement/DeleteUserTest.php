<?php

use Domain\User\Entities\User;
use Shared\Enums\RoleName;

beforeEach(fn () => setUpPassport());

it('soft deletes a user', function () {
    actingAsRole(RoleName::Admin);
    $user = User::factory()->create();

    $this->deleteJson("/api/users/{$user->id}")->assertNoContent();

    expect(User::find($user->id))->toBeNull()
        ->and(User::withTrashed()->find($user->id)?->deleted_at)->not->toBeNull();
});

it('stops a deleted user from signing in or using an old token', function () {
    $user = User::factory()->create(['username' => 'leaving']);
    $token = $this->postJson('/api/auth/login', ['login' => 'leaving', 'password' => 'password'])->json('token');

    actingAsRole(RoleName::Admin);
    $this->deleteJson("/api/users/{$user->id}")->assertNoContent();

    $this->postJson('/api/auth/login', ['login' => 'leaving', 'password' => 'password'])->assertUnauthorized();

    $this->app['auth']->forgetGuards();
    $this->withToken($token)->getJson('/api/auth/me')->assertUnauthorized();
});

it('revokes the tokens of the deleted user', function () {
    $user = User::factory()->create(['username' => 'leaving']);
    $this->postJson('/api/auth/login', ['login' => 'leaving', 'password' => 'password'])->assertOk();

    actingAsRole(RoleName::Admin);
    $this->deleteJson("/api/users/{$user->id}")->assertNoContent();

    expect($user->tokens()->where('revoked', false)->count())->toBe(0);
});

it('does not let an admin delete their own account', function () {
    $admin = actingAsRole(RoleName::Admin);

    $this->deleteJson("/api/users/{$admin->id}")
        ->assertStatus(409)
        ->assertJsonPath('message', 'You cannot delete your own account.');

    expect(User::find($admin->id))->not->toBeNull();
});

it('answers 404 for an unknown user', function () {
    actingAsRole(RoleName::Admin);

    $this->deleteJson('/api/users/'.fake()->uuid())->assertNotFound();
});

it('is limited to roles that may delete users', function () {
    $user = User::factory()->create();
    actingAsRole(RoleName::Hr);

    $this->deleteJson("/api/users/{$user->id}")->assertForbidden();
});
