<?php

use Domain\City\Entities\City;
use Domain\Country\Entities\Country;
use Domain\User\Entities\User;
use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Support\Facades\Schema;
use Shared\Enums\RoleName;

function workAreaPayload(array $overrides = []): array
{
    $city = City::factory()->create();

    return array_merge([
        'name' => 'Head office',
        'address' => '1 Main St',
        'country_id' => $city->country_id,
        'city_id' => $city->id,
        'manager_id' => User::factory()->create()->id,
        'phone' => '+20 1012345678',
        'email' => 'Office@Example.com',
        'status' => 'open',
        'opening_hours' => '09:00-17:00',
        'description' => 'Main branch',
    ], $overrides);
}

it('names the foreign keys city_id and country_id', function () {
    expect(Schema::hasColumns('work_areas', ['city_id', 'country_id']))->toBeTrue()
        ->and(Schema::hasColumn('work_areas', 'city'))->toBeFalse()
        ->and(Schema::hasColumn('work_areas', 'country'))->toBeFalse();
});

describe('as admin', function () {
    beforeEach(function () {
        actingAsRole(RoleName::Admin);
    });

    it('creates a work area', function () {
        $this->postJson('/api/work-areas', workAreaPayload())
            ->assertCreated()
            ->assertJsonPath('data.name', 'Head office')
            ->assertJsonPath('data.status', 'open')
            ->assertJsonPath('data.email', 'office@example.com');

        expect(WorkArea::count())->toBe(1);
    });

    it('defaults the status to open', function () {
        $this->postJson('/api/work-areas', workAreaPayload(['status' => null]))
            ->assertCreated()
            ->assertJsonPath('data.status', 'open');
    });

    it('rejects a status outside the enum', function () {
        $this->postJson('/api/work-areas', workAreaPayload(['status' => 'haunted']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('status');
    });

    it('accepts every status', function (string $status) {
        $this->postJson('/api/work-areas', workAreaPayload(['status' => $status]))
            ->assertCreated()
            ->assertJsonPath('data.status', $status);
    })->with(['open', 'closed', 'under_maintenance']);

    it('requires an active manager', function (string $state) {
        $manager = User::factory()->{$state}()->create();

        $this->postJson('/api/work-areas', workAreaPayload(['manager_id' => $manager->id]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('manager_id');
    })->with(['inactive', 'terminated']);

    it('rejects a deleted or unknown manager', function () {
        $deleted = User::factory()->create();
        $deleted->delete();

        $this->postJson('/api/work-areas', workAreaPayload(['manager_id' => $deleted->id]))
            ->assertUnprocessable()->assertJsonValidationErrors('manager_id');
        $this->postJson('/api/work-areas', workAreaPayload(['manager_id' => fake()->uuid()]))
            ->assertUnprocessable()->assertJsonValidationErrors('manager_id');
    });

    it('requires the city to belong to the country', function () {
        $city = City::factory()->create();
        $otherCountry = Country::factory()->create();

        $this->postJson('/api/work-areas', workAreaPayload(['city_id' => $city->id, 'country_id' => $otherCountry->id]))
            ->assertUnprocessable()
            ->assertJsonPath('errors.city_id.0', 'The selected city does not belong to the selected country.');
    });

    it('rejects a malformed phone and email', function () {
        $this->postJson('/api/work-areas', workAreaPayload(['phone' => 'call me', 'email' => 'nope']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['phone', 'email']);
    });

    it('shows a work area with its relations on request', function () {
        $area = WorkArea::factory()->create();

        $this->getJson("/api/work-areas/{$area->id}")
            ->assertOk()
            ->assertJsonMissingPath('data.manager');

        $this->getJson("/api/work-areas/{$area->id}?include=city,country,manager")
            ->assertOk()
            ->assertJsonPath('data.city.id', $area->city_id)
            ->assertJsonPath('data.country.id', $area->country_id)
            ->assertJsonPath('data.manager.id', $area->manager_id);
    });

    it('answers 404 for an unknown work area', function () {
        $this->getJson('/api/work-areas/999')->assertNotFound();
        $this->putJson('/api/work-areas/999', [])->assertNotFound();
        $this->deleteJson('/api/work-areas/999')->assertNotFound();
    });

    it('updates a work area', function () {
        $area = WorkArea::factory()->create();

        $this->putJson("/api/work-areas/{$area->id}", workAreaPayload([
            'name' => 'Renamed', 'status' => 'closed',
            'country_id' => $area->country_id, 'city_id' => $area->city_id, 'manager_id' => $area->manager_id,
        ]))->assertOk()->assertJsonPath('data.status', 'closed');

        expect($area->fresh()->name)->toBe('Renamed');
    });

    it('lists work areas and filters by city, country and status', function () {
        $cairo = City::factory()->create();
        $alex = City::factory()->create();
        WorkArea::factory()->create(['name' => 'A', 'city_id' => $cairo->id, 'country_id' => $cairo->country_id]);
        WorkArea::factory()->closed()->create(['name' => 'B', 'city_id' => $cairo->id, 'country_id' => $cairo->country_id]);
        WorkArea::factory()->underMaintenance()->create(['name' => 'C', 'city_id' => $alex->id, 'country_id' => $alex->country_id]);

        $this->getJson('/api/work-areas')->assertOk()->assertJsonPath('meta.total', 3);
        $this->getJson("/api/work-areas?filter[city_id]={$cairo->id}")->assertOk()->assertJsonPath('data.*.name', ['A', 'B']);
        $this->getJson("/api/work-areas?filter[country_id]={$alex->country_id}")->assertOk()->assertJsonPath('data.*.name', ['C']);
        $this->getJson('/api/work-areas?filter[status]=closed')->assertOk()->assertJsonPath('data.*.name', ['B']);
        $this->getJson('/api/work-areas?filter[status]=gone')->assertUnprocessable();
    });

    it('deletes an empty work area and refuses one with employees', function () {
        $empty = WorkArea::factory()->create();
        $busy = WorkArea::factory()->create();
        User::factory()->create(['work_area_id' => $busy->id]);

        $this->deleteJson("/api/work-areas/{$empty->id}")->assertNoContent();
        $this->deleteJson("/api/work-areas/{$busy->id}")->assertStatus(409);

        expect(WorkArea::count())->toBe(1);
    });

    it('protects a city and a country that a work area uses', function () {
        $area = WorkArea::factory()->create();

        $this->deleteJson("/api/cities/{$area->city_id}")->assertStatus(409);
        $this->deleteJson("/api/countries/{$area->country_id}")->assertStatus(409);
    });
});

describe('permissions', function () {
    it('lets HR and managers read but not change work areas', function (RoleName $role) {
        actingAsRole($role);
        $area = WorkArea::factory()->create();

        $this->getJson('/api/work-areas')->assertOk();
        $this->postJson('/api/work-areas', workAreaPayload())->assertForbidden();
        $this->putJson("/api/work-areas/{$area->id}", workAreaPayload())->assertForbidden();
        $this->deleteJson("/api/work-areas/{$area->id}")->assertForbidden();
    })->with([RoleName::Hr, RoleName::Manager]);

    it('keeps work areas away from employees', function () {
        actingAsRole(RoleName::Employee);

        $this->getJson('/api/work-areas')->assertForbidden();
    });
});
