<?php

use Domain\User\Entities\User;
use Illuminate\Support\Facades\Hash;
use Shared\Enums\RoleName;
use Shared\Enums\UserStatus;

it('creates a user with a role and stores normalized values', function () {
    actingAsRole(RoleName::Hr);

    $response = $this->postJson('/api/users', userPayload())
        ->assertCreated()
        ->assertJsonPath('data.username', 'sara.hassan')
        ->assertJsonPath('data.email', 'sara@example.com')
        ->assertJsonPath('data.name.full', 'Sara Hassan')
        ->assertJsonPath('data.status', 'active');

    $user = User::findOrFail($response->json('data.id'));

    expect($user->hasRole('employee'))->toBeTrue()
        ->and(Hash::check('a-strong-password', $user->password))->toBeTrue()
        ->and($user->password_changed_at)->not->toBeNull()
        ->and((string) $user->phone_number)->toBe('+20 1098765432');
});

it('does not expose the password or salary in the response', function () {
    actingAsRole(RoleName::Hr);

    $this->postJson('/api/users', userPayload())
        ->assertCreated()
        ->assertJsonMissingPath('data.password')
        ->assertJsonMissingPath('data.salary');
});

it('is limited to roles that may create users', function () {
    actingAsRole(RoleName::Employee);
    $payload = userPayload();
    $before = User::count();

    $this->postJson('/api/users', $payload)->assertForbidden();

    expect(User::count())->toBe($before);
});

it('requires authentication', function () {
    $this->postJson('/api/users', userPayload())->assertUnauthorized();
});

it('validates required fields', function () {
    actingAsRole(RoleName::Admin);

    $this->postJson('/api/users', [])->assertUnprocessable()->assertJsonValidationErrors([
        'name.first', 'username', 'email', 'phone.country_code', 'phone.number', 'country_id',
        'city_id', 'position_id', 'work_area_id', 'work_shift_id', 'birth_date',
        'start_working_date', 'salary', 'role', 'password',
    ]);
});

it('rejects duplicate username, email and phone', function () {
    actingAsRole(RoleName::Admin);
    $payload = userPayload();
    $this->postJson('/api/users', $payload)->assertCreated();

    $this->postJson('/api/users', array_merge($payload, [
        'email' => 'SARA@example.com',
        'username' => 'sara.hassan',
        'phone' => ['country_code' => '0020', 'number' => '1098765432'],
    ]))->assertUnprocessable()->assertJsonValidationErrors(['username', 'email']);

    $this->postJson('/api/users', array_merge($payload, ['username' => 'other', 'email' => 'other@example.com']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['phone.number']);
});

it('rejects references to rows that do not exist', function () {
    actingAsRole(RoleName::Admin);

    $this->postJson('/api/users', userPayload(['position_id' => 9999]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['position_id']);
});

it('rejects an unknown role, a weak password and an end date before the start date', function () {
    actingAsRole(RoleName::Admin);

    $this->postJson('/api/users', userPayload([
        'role' => 'root', 'password' => 'short', 'end_working_date' => '2020-01-01',
    ]))->assertUnprocessable()->assertJsonValidationErrors(['role', 'password', 'end_working_date']);
});

it('stores optional flags and status', function () {
    actingAsRole(RoleName::Admin);

    $id = $this->postJson('/api/users', userPayload(['is_remote' => true, 'status' => 'inactive']))
        ->assertCreated()
        ->json('data.id');

    $user = User::findOrFail($id);

    expect($user->is_remote)->toBeTrue()->and($user->status)->toBe(UserStatus::Inactive);
});
