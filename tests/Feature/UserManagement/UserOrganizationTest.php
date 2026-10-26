<?php

use Domain\City\Entities\City;
use Domain\Country\Entities\Country;
use Domain\User\Entities\User;
use Domain\WorkArea\Entities\WorkArea;
use Domain\WorkShift\Entities\WorkShift;
use Illuminate\Support\Facades\DB;
use Shared\Enums\RoleName;

const ORGANIZATION_INCLUDES = 'country,city,position,workArea,workShift';

beforeEach(function () {
    actingAsRole(RoleName::Hr);
});

it('exposes the organization relations of a user', function () {
    $user = User::factory()->create();
    $org = organization();
    $user->update($org);

    expect($user->country->id)->toBe($org['country_id'])
        ->and($user->city->id)->toBe($org['city_id'])
        ->and($user->position->id)->toBe($org['position_id'])
        ->and($user->workArea->id)->toBe($org['work_area_id'])
        ->and($user->workShift->id)->toBe($org['work_shift_id']);
});

it('returns the foreign keys without an include', function () {
    $org = organization();
    $user = User::factory()->create($org);

    $this->getJson("/api/users/{$user->id}")
        ->assertOk()
        ->assertJsonPath('data.country_id', $org['country_id'])
        ->assertJsonPath('data.work_shift_id', $org['work_shift_id'])
        ->assertJsonMissingPath('data.country')
        ->assertJsonMissingPath('data.work_area');
});

it('includes the organization on a single user', function () {
    $org = organization();
    $user = User::factory()->create($org);

    $this->getJson("/api/users/{$user->id}?include=".ORGANIZATION_INCLUDES)
        ->assertOk()
        ->assertJsonPath('data.country.id', $org['country_id'])
        ->assertJsonPath('data.city.id', $org['city_id'])
        ->assertJsonPath('data.position.id', $org['position_id'])
        ->assertJsonPath('data.work_area.id', $org['work_area_id'])
        ->assertJsonPath('data.work_shift.id', $org['work_shift_id'])
        ->assertJsonPath('data.work_shift.start_time', '09:00');
});

it('includes the organization in the list without a query per user', function () {
    foreach (range(1, 5) as $ignored) {
        User::factory()->create(organization());
    }

    $this->getJson('/api/users?include='.ORGANIZATION_INCLUDES)->assertOk();
    $queries = 0;
    DB::listen(function () use (&$queries) {
        $queries++;
    });

    $this->getJson('/api/users?per_page=50&include='.ORGANIZATION_INCLUDES)
        ->assertOk()
        ->assertJsonCount(User::count(), 'data');

    expect($queries)->toBeLessThanOrEqual(14);
});

it('still rejects relations that are not allowed', function () {
    $user = User::factory()->create();

    $this->getJson("/api/users/{$user->id}?include=manager")->assertStatus(400);
});

it('requires the city to belong to the country', function () {
    $org = organization();
    $otherCountry = Country::factory()->create();

    $this->postJson('/api/users', userPayload(['country_id' => $otherCountry->id, 'city_id' => $org['city_id']]))
        ->assertUnprocessable()
        ->assertJsonPath('errors.city_id.0', 'The selected city does not belong to the selected country.');
});

it('requires the shift to belong to the work area', function () {
    $area = WorkArea::factory()->create();
    $strangerShift = WorkShift::factory()->create();

    $this->postJson('/api/users', userPayload(['work_area_id' => $area->id, 'work_shift_id' => $strangerShift->id]))
        ->assertUnprocessable()
        ->assertJsonPath('errors.work_shift_id.0', 'The selected shift does not belong to the selected work area.');
});

it('accepts a city of the chosen country and a shift of the chosen work area', function () {
    $city = City::factory()->create();
    $area = WorkArea::factory()->create();
    $shift = WorkShift::factory()->create(['work_area_id' => $area->id]);

    $this->postJson('/api/users', userPayload([
        'country_id' => $city->country_id, 'city_id' => $city->id,
        'work_area_id' => $area->id, 'work_shift_id' => $shift->id,
    ]))->assertCreated()->assertJsonPath('data.city_id', $city->id);
});

it('applies the same rules when updating a user', function () {
    $id = $this->postJson('/api/users', userPayload())->assertCreated()->json('data.id');
    $otherCountry = Country::factory()->create();
    $city = City::factory()->create();

    $this->putJson("/api/users/{$id}", userPayload([
        'country_id' => $otherCountry->id, 'city_id' => $city->id,
        'role' => null, 'password' => null,
    ]))->assertUnprocessable()->assertJsonValidationErrors('city_id');
});

it('rejects references to rows that do not exist', function () {
    $this->postJson('/api/users', userPayload(['country_id' => 9999, 'city_id' => 9999, 'position_id' => 9999, 'work_area_id' => 9999, 'work_shift_id' => 9999]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['country_id', 'city_id', 'position_id', 'work_area_id', 'work_shift_id']);
});
