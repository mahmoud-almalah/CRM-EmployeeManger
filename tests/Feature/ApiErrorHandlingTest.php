<?php

use Domain\User\Entities\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::get('/api/_test/validation', fn (Request $request) => $request->validate(['name' => 'required']));
    Route::get('/api/_test/missing-model', fn () => User::findOrFail('does-not-exist'));
    Route::get('/api/_test/guarded', fn () => 'secret')->middleware('auth');
    Route::get('/api/_test/forbidden', fn () => throw new AuthorizationException);
    Route::get('/api/_test/boom', fn () => throw new RuntimeException('database password is hunter2'));
});

it('returns 422 with field errors for invalid input', function () {
    $this->getJson('/api/_test/validation')
        ->assertStatus(422)
        ->assertJsonPath('message', 'The given data was invalid.')
        ->assertJsonValidationErrors('name');
});

it('returns 404 without naming the model', function () {
    $this->getJson('/api/_test/missing-model')
        ->assertNotFound()
        ->assertExactJson(['message' => 'Resource not found.']);
});

it('returns 404 for an unknown route', function () {
    $this->getJson('/api/nothing-here')->assertNotFound()->assertJsonStructure(['message']);
});

it('returns 401 for a guest', function () {
    $this->getJson('/api/_test/guarded')
        ->assertUnauthorized()
        ->assertExactJson(['message' => 'Unauthenticated.']);
});

it('returns 403 when the action is not authorized', function () {
    $this->getJson('/api/_test/forbidden')->assertForbidden()->assertJsonStructure(['message']);
});

it('hides the cause of an unexpected error outside debug mode', function () {
    config(['app.debug' => false]);

    $this->getJson('/api/_test/boom')
        ->assertStatus(500)
        ->assertExactJson(['message' => 'Server error.']);
});

it('returns json even when the client does not ask for it on an api route', function () {
    $this->get('/api/nothing-here')->assertNotFound()->assertJsonStructure(['message']);
});
