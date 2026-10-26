<?php

use Domain\Country\Entities\Country;
use Domain\User\Entities\User;
use Illuminate\Support\Facades\DB;
use Shared\Enums\RoleName;

function countryPayload(array $overrides = []): array
{
    return array_merge(['name' => 'Egypt', 'phone_code' => '+20', 'currency' => 'EGP'], $overrides);
}

describe('as admin', function () {
    beforeEach(function () {
        actingAsRole(RoleName::Admin);
    });

    it('creates a country', function () {
        $this->postJson('/api/countries', countryPayload())
            ->assertCreated()
            ->assertJsonPath('data.name', 'Egypt')
            ->assertJsonPath('data.phone_code', '+20')
            ->assertJsonPath('data.currency', 'EGP');

        expect(Country::count())->toBe(1);
    });

    it('allows a country without a currency', function () {
        $this->postJson('/api/countries', countryPayload(['currency' => null]))
            ->assertCreated()
            ->assertJsonPath('data.currency', null);
    });

    it('rejects a currency outside the enum', function () {
        $this->postJson('/api/countries', countryPayload(['currency' => 'XYZ']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('currency');
    });

    it('rejects a malformed phone code', function (string $code) {
        $this->postJson('/api/countries', countryPayload(['phone_code' => $code]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('phone_code');
    })->with(['20', '+', '+12345', '+2a', '++20']);

    it('requires a unique name', function () {
        Country::factory()->create(['name' => 'Egypt']);

        $this->postJson('/api/countries', countryPayload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    });

    it('shows one country', function () {
        $country = Country::factory()->create();

        $this->getJson("/api/countries/{$country->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $country->id)
            ->assertJsonPath('data.name', $country->name);
    });

    it('answers 404 for an unknown country', function () {
        $this->getJson('/api/countries/999')->assertNotFound();
        $this->putJson('/api/countries/999', [])->assertNotFound();
        $this->deleteJson('/api/countries/999')->assertNotFound();
    });

    it('updates a country and lets it keep its own name', function () {
        $country = Country::factory()->create(['name' => 'Egypt', 'phone_code' => '+20']);

        $this->putJson("/api/countries/{$country->id}", countryPayload(['phone_code' => '+21']))
            ->assertOk()
            ->assertJsonPath('data.phone_code', '+21');

        expect($country->fresh()->phone_code)->toBe('+21');
    });

    it('does not let an update take another country name', function () {
        Country::factory()->create(['name' => 'Egypt']);
        $other = Country::factory()->create(['name' => 'Jordan']);

        $this->putJson("/api/countries/{$other->id}", countryPayload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    });

    it('lists countries sorted by name and filters them', function () {
        Country::factory()->create(['name' => 'Egypt', 'currency' => 'EGP']);
        Country::factory()->create(['name' => 'Japan', 'currency' => 'JPY']);
        Country::factory()->create(['name' => 'Germany', 'currency' => 'EUR']);

        $this->getJson('/api/countries')
            ->assertOk()
            ->assertJsonPath('data.*.name', ['Egypt', 'Germany', 'Japan'])
            ->assertJsonPath('meta.total', 3);

        $this->getJson('/api/countries?filter[currency]=JPY')->assertOk()->assertJsonPath('data.*.name', ['Japan']);
        $this->getJson('/api/countries?filter[name]=erman')->assertOk()->assertJsonPath('data.*.name', ['Germany']);
        $this->getJson('/api/countries?sort=-name')->assertOk()->assertJsonPath('data.0.name', 'Japan');
    });

    it('rejects an unknown filter', function () {
        $this->getJson('/api/countries?filter[colour]=red')->assertStatus(400);
    });

    it('deletes an unused country', function () {
        $country = Country::factory()->create();

        $this->deleteJson("/api/countries/{$country->id}")->assertNoContent();

        expect(Country::count())->toBe(0);
    });

    it('refuses to delete a country that has cities', function () {
        $country = Country::factory()->create();
        DB::table('cities')->insert(['country_id' => $country->id, 'name' => 'Cairo', 'created_at' => now(), 'updated_at' => now()]);

        $this->deleteJson("/api/countries/{$country->id}")
            ->assertStatus(409)
            ->assertJsonPath('message', 'The country is still used by cities, work areas or users and cannot be deleted.');

        expect(Country::count())->toBe(1);
    });

    it('refuses to delete a country that has users', function () {
        $country = Country::factory()->create();
        User::factory()->create(['country_id' => $country->id]);

        $this->deleteJson("/api/countries/{$country->id}")->assertStatus(409);
    });
});

describe('permissions', function () {
    it('lets HR read but not change countries', function () {
        actingAsRole(RoleName::Hr);
        $country = Country::factory()->create();

        $this->getJson('/api/countries')->assertOk();
        $this->postJson('/api/countries', countryPayload())->assertForbidden();
        $this->putJson("/api/countries/{$country->id}", countryPayload())->assertForbidden();
        $this->deleteJson("/api/countries/{$country->id}")->assertForbidden();
    });

    it('keeps countries away from employees', function () {
        actingAsRole(RoleName::Employee);

        $this->getJson('/api/countries')->assertForbidden();
    });

    it('requires a token', function () {
        $this->getJson('/api/countries')->assertUnauthorized();
    });
});
