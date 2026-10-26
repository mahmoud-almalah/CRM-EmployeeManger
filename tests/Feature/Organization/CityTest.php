<?php

use Domain\City\Entities\City;
use Domain\Country\Entities\Country;
use Domain\User\Entities\User;
use Shared\Enums\RoleName;

function cityPayload(array $overrides = []): array
{
    return array_merge([
        'country_id' => Country::factory()->create()->id,
        'name' => 'Cairo',
        'code' => 'CAI',
        'coordinates' => ['lat' => 30.0444, 'lng' => 31.2357],
        'timezone' => 'Africa/Cairo',
    ], $overrides);
}

describe('as admin', function () {
    beforeEach(function () {
        actingAsRole(RoleName::Admin);
    });

    it('creates a city with coordinates and a timezone', function () {
        $this->postJson('/api/cities', cityPayload())
            ->assertCreated()
            ->assertJsonPath('data.name', 'Cairo')
            ->assertJsonPath('data.coordinates.lat', 30.0444)
            ->assertJsonPath('data.coordinates.lng', 31.2357)
            ->assertJsonPath('data.timezone', 'Africa/Cairo');

        expect(City::count())->toBe(1)
            ->and(City::first()->coordinates)->toBe(['lat' => 30.0444, 'lng' => 31.2357]);
    });

    it('allows a city without coordinates, code or timezone', function () {
        $this->postJson('/api/cities', cityPayload(['coordinates' => null, 'code' => null, 'timezone' => null]))
            ->assertCreated()
            ->assertJsonPath('data.coordinates', null);
    });

    it('rejects coordinates that are out of range or incomplete', function (array $coordinates, string $field) {
        $this->postJson('/api/cities', cityPayload(['coordinates' => $coordinates]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    })->with([
        'latitude too high' => [['lat' => 91, 'lng' => 10], 'coordinates.lat'],
        'longitude too low' => [['lat' => 10, 'lng' => -181], 'coordinates.lng'],
        'missing longitude' => [['lat' => 10], 'coordinates.lng'],
        'not numbers' => [['lat' => 'north', 'lng' => 'east'], 'coordinates.lat'],
    ]);

    it('rejects an unknown timezone', function () {
        $this->postJson('/api/cities', cityPayload(['timezone' => 'Mars/Olympus']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('timezone');
    });

    it('rejects a country that does not exist', function () {
        $this->postJson('/api/cities', cityPayload(['country_id' => 999]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('country_id');
    });

    it('keeps a city name unique inside its country only', function () {
        $egypt = Country::factory()->create();
        $other = Country::factory()->create();
        City::factory()->create(['country_id' => $egypt->id, 'name' => 'Alexandria']);

        $this->postJson('/api/cities', cityPayload(['country_id' => $egypt->id, 'name' => 'Alexandria']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        $this->postJson('/api/cities', cityPayload(['country_id' => $other->id, 'name' => 'Alexandria']))
            ->assertCreated();
    });

    it('shows a city and includes its country on request', function () {
        $city = City::factory()->create();

        $this->getJson("/api/cities/{$city->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $city->id)
            ->assertJsonMissingPath('data.country');

        $this->getJson("/api/cities/{$city->id}?include=country")
            ->assertOk()
            ->assertJsonPath('data.country.id', $city->country_id);
    });

    it('answers 404 for an unknown city', function () {
        $this->getJson('/api/cities/999')->assertNotFound();
        $this->putJson('/api/cities/999', [])->assertNotFound();
        $this->deleteJson('/api/cities/999')->assertNotFound();
    });

    it('updates a city and lets it keep its own name', function () {
        $city = City::factory()->create(['name' => 'Cairo']);

        $this->putJson("/api/cities/{$city->id}", cityPayload(['country_id' => $city->country_id, 'code' => 'CAIRO']))
            ->assertOk()
            ->assertJsonPath('data.code', 'CAIRO');

        expect($city->fresh()->code)->toBe('CAIRO');
    });

    it('lists cities and filters them by country', function () {
        $egypt = Country::factory()->create();
        $japan = Country::factory()->create();
        City::factory()->create(['country_id' => $egypt->id, 'name' => 'Cairo']);
        City::factory()->create(['country_id' => $egypt->id, 'name' => 'Giza']);
        City::factory()->create(['country_id' => $japan->id, 'name' => 'Tokyo']);

        $this->getJson('/api/cities')->assertOk()->assertJsonPath('meta.total', 3);

        $this->getJson("/api/cities?filter[country_id]={$egypt->id}")
            ->assertOk()
            ->assertJsonPath('data.*.name', ['Cairo', 'Giza']);

        $this->getJson('/api/cities?filter[name]=tok&sort=-name')
            ->assertOk()
            ->assertJsonPath('data.*.name', ['Tokyo']);
    });

    it('deletes an unused city', function () {
        $city = City::factory()->create();

        $this->deleteJson("/api/cities/{$city->id}")->assertNoContent();

        expect(City::count())->toBe(0);
    });

    it('refuses to delete a city that users live in', function () {
        $city = City::factory()->create();
        User::factory()->create(['city_id' => $city->id]);

        $this->deleteJson("/api/cities/{$city->id}")->assertStatus(409);

        expect(City::count())->toBe(1);
    });
});

describe('permissions', function () {
    it('lets HR read but not change cities', function () {
        actingAsRole(RoleName::Hr);
        $city = City::factory()->create();

        $this->getJson('/api/cities')->assertOk();
        $this->postJson('/api/cities', cityPayload())->assertForbidden();
        $this->putJson("/api/cities/{$city->id}", cityPayload())->assertForbidden();
        $this->deleteJson("/api/cities/{$city->id}")->assertForbidden();
    });

    it('keeps cities away from employees', function () {
        actingAsRole(RoleName::Employee);

        $this->getJson('/api/cities')->assertForbidden();
    });
});

it('lists the cities of a country through the relation', function () {
    $country = Country::factory()->has(City::factory()->count(2))->create();

    expect($country->cities)->toHaveCount(2)
        ->and($country->cities->first()->country->is($country))->toBeTrue();
});
