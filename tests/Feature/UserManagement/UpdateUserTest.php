<?php

use Domain\User\Entities\User;
use Illuminate\Support\Facades\Hash;
use Shared\Enums\RoleName;

function existingUser(): User
{
    actingAsRole(RoleName::Hr);
    $id = test()->postJson('/api/users', userPayload())->assertCreated()->json('data.id');

    return User::findOrFail($id);
}

function updatePayload(User $user, array $overrides = []): array
{
    return array_merge(userPayload(), [
        'username' => $user->username,
        'email' => $user->email->getEmail(),
        'phone' => ['country_code' => '+20', 'number' => '1098765432'],
    ], $overrides);
}

it('updates the user and keeps the password when none is sent', function () {
    $user = existingUser();
    $before = $user->password;

    $this->putJson("/api/users/{$user->id}", updatePayload($user, [
        'name' => ['first' => 'Sarah', 'last' => 'Hassan'], 'salary' => 3000, 'password' => null, 'role' => null,
    ]))->assertOk()->assertJsonPath('data.name.full', 'Sarah Hassan');

    $fresh = $user->fresh();

    expect($fresh->name->first)->toBe('Sarah')
        ->and((float) $fresh->salary)->toBe(3000.0)
        ->and($fresh->password)->toBe($before)
        ->and($fresh->hasRole('employee'))->toBeTrue();
});

it('stamps password_changed_at when the password changes', function () {
    $user = existingUser();
    $user->forceFill(['password_changed_at' => null])->save();

    $this->putJson("/api/users/{$user->id}", updatePayload($user, ['password' => 'a-brand-new-password']))->assertOk();

    $fresh = $user->fresh();

    expect(Hash::check('a-brand-new-password', $fresh->password))->toBeTrue()
        ->and($fresh->password_changed_at)->not->toBeNull();
});

it('changes the role only when one is sent', function () {
    $user = existingUser();

    $this->putJson("/api/users/{$user->id}", updatePayload($user, ['role' => 'manager']))->assertOk();

    expect($user->fresh()->hasRole('manager'))->toBeTrue()
        ->and($user->fresh()->hasRole('employee'))->toBeFalse();
});

it('lets the user keep their own username, email and phone', function () {
    $user = existingUser();

    $this->putJson("/api/users/{$user->id}", updatePayload($user))->assertOk();
});

it('still rejects values that belong to someone else', function () {
    $user = existingUser();
    $other = User::factory()->create();

    $this->putJson("/api/users/{$user->id}", updatePayload($user, [
        'username' => $other->username, 'email' => $other->email->getEmail(),
    ]))->assertUnprocessable()->assertJsonValidationErrors(['username', 'email']);
});

it('answers 404 for an unknown user', function () {
    $user = existingUser();

    $this->putJson('/api/users/'.fake()->uuid(), updatePayload($user))->assertNotFound();
});

it('is limited to roles that may update users', function () {
    $user = existingUser();
    actingAsRole(RoleName::Employee);

    $this->putJson("/api/users/{$user->id}", updatePayload($user))->assertForbidden();
});
