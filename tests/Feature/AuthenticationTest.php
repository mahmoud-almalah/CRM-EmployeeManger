<?php

use Domain\User\Entities\User;

beforeEach(fn () => setUpPassport());

function login(string $login, string $password = 'password')
{
    return test()->postJson('/api/auth/login', ['login' => $login, 'password' => $password]);
}

it('signs in with the username and returns a bearer token', function () {
    $user = User::factory()->create(['username' => 'mahmoud']);

    login('mahmoud')
        ->assertOk()
        ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'name', 'email']])
        ->assertJsonPath('token_type', 'Bearer')
        ->assertJsonPath('user.id', $user->id);
});

it('signs in with the email, ignoring case', function () {
    User::factory()->create(['email' => new Domain\User\ValueObjects\EmailObject('mahmoud@example.com')]);

    login('Mahmoud@Example.com')->assertOk();
});

it('rejects a wrong password and an unknown account the same way', function () {
    User::factory()->create(['username' => 'mahmoud']);

    login('mahmoud', 'wrong')->assertUnauthorized()->assertExactJson(['message' => 'Invalid credentials.']);
    login('nobody')->assertUnauthorized()->assertExactJson(['message' => 'Invalid credentials.']);
});

it('refuses terminated and inactive accounts', function () {
    User::factory()->terminated()->create(['username' => 'gone']);
    User::factory()->inactive()->create(['username' => 'paused']);

    login('gone')->assertForbidden();
    login('paused')->assertForbidden();
});

it('validates the login payload', function () {
    $this->postJson('/api/auth/login', [])->assertUnprocessable()->assertJsonValidationErrors(['login', 'password']);
});

it('returns the signed in user', function () {
    User::factory()->create(['username' => 'mahmoud']);
    $token = login('mahmoud')->json('token');

    $this->withToken($token)->getJson('/api/auth/me')
        ->assertOk()
        ->assertJsonPath('data.username', 'mahmoud');
});

it('requires a token for the profile', function () {
    $this->getJson('/api/auth/me')->assertUnauthorized();
});

it('revokes the token on logout', function () {
    User::factory()->create(['username' => 'mahmoud']);
    $token = login('mahmoud')->json('token');

    $this->withToken($token)->postJson('/api/auth/logout')->assertNoContent();

    $this->app['auth']->forgetGuards();
    $this->withToken($token)->getJson('/api/auth/me')->assertUnauthorized();
});

it('protects the user routes', function () {
    $this->getJson('/api/users')->assertUnauthorized();
});
