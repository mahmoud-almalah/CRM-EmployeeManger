<?php

use Domain\Supervisor\Entities\Supervisor;
use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Shared\Enums\RoleName;

function supervisorPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'Omar Khaled',
        'email' => 'Omar@Example.com',
        'phone' => ['country_code' => '+20', 'number' => '1055555555'],
        'status' => 'active',
        'password' => 'a-strong-password',
        'profile_picture' => 'https://example.com/omar.png',
    ], $overrides);
}

it('stores the supervisor status as a string', function () {
    expect(Schema::getColumnType('supervisors', 'status'))->toBeIn(['varchar', 'string', 'text']);
});

describe('as admin', function () {
    beforeEach(function () {
        actingAsRole(RoleName::Admin);
    });

    it('creates a supervisor and hashes the password', function () {
        $id = $this->postJson('/api/supervisors', supervisorPayload())
            ->assertCreated()
            ->assertJsonPath('data.name', 'Omar Khaled')
            ->assertJsonPath('data.email', 'omar@example.com')
            ->assertJsonPath('data.phone.number', '1055555555')
            ->assertJsonPath('data.status', 'active')
            ->assertJsonMissingPath('data.password')
            ->json('data.id');

        $supervisor = Supervisor::findOrFail($id);

        expect($supervisor->password)->not->toBe('a-strong-password')
            ->and(Hash::check('a-strong-password', $supervisor->password))->toBeTrue()
            ->and((string) $supervisor->phone_number)->toBe('+20 1055555555');
    });

    it('validates the payload', function (array $overrides, string $field) {
        $this->postJson('/api/supervisors', supervisorPayload($overrides))
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    })->with([
        'missing name' => [['name' => ''], 'name'],
        'bad email' => [['email' => 'nope'], 'email'],
        'short password' => [['password' => 'short'], 'password'],
        'missing password' => [['password' => null], 'password'],
        'unknown status' => [['status' => 'suspended'], 'status'],
        'bad phone' => [['phone' => ['country_code' => '+20', 'number' => 'abc']], 'phone.number'],
    ]);

    it('keeps email and phone unique', function () {
        Supervisor::factory()->create([
            'email' => 'omar@example.com',
            'phone_number' => new PhoneObject('1055555555', '+20'),
        ]);

        $this->postJson('/api/supervisors', supervisorPayload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email', 'phone.number']);
    });

    it('shows a supervisor and answers 404 for an unknown one', function () {
        $supervisor = Supervisor::factory()->create();

        $this->getJson("/api/supervisors/{$supervisor->id}")->assertOk()->assertJsonPath('data.id', $supervisor->id);
        $this->getJson('/api/supervisors/'.fake()->uuid())->assertNotFound();
        $this->putJson('/api/supervisors/'.fake()->uuid(), supervisorPayload())->assertNotFound();
        $this->deleteJson('/api/supervisors/'.fake()->uuid())->assertNotFound();
    });

    it('updates a supervisor, keeping the password when none is sent and their own email and phone', function () {
        $supervisor = Supervisor::factory()->create([
            'email' => 'omar@example.com',
            'phone_number' => new PhoneObject('1055555555', '+20'),
        ]);
        $before = $supervisor->password;

        $this->putJson("/api/supervisors/{$supervisor->id}", supervisorPayload(['name' => 'Omar K.', 'status' => 'inactive', 'password' => null]))
            ->assertOk()
            ->assertJsonPath('data.name', 'Omar K.')
            ->assertJsonPath('data.status', 'inactive');

        expect($supervisor->fresh()->password)->toBe($before);
    });

    it('changes the password when a new one is sent', function () {
        $supervisor = Supervisor::factory()->create();

        $this->putJson("/api/supervisors/{$supervisor->id}", supervisorPayload(['password' => 'another-strong-one']))->assertOk();

        expect(Hash::check('another-strong-one', $supervisor->fresh()->password))->toBeTrue();
    });

    it('does not let an update take another supervisor email', function () {
        Supervisor::factory()->create(['email' => 'omar@example.com']);
        $other = Supervisor::factory()->create();

        $this->putJson("/api/supervisors/{$other->id}", supervisorPayload())
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    });

    it('lists and filters supervisors', function () {
        Supervisor::factory()->create(['name' => 'Amal', 'email' => 'amal@corp.test']);
        Supervisor::factory()->inactive()->create(['name' => 'Bassem', 'email' => 'bassem@other.test']);

        $this->getJson('/api/supervisors?sort=name')->assertOk()->assertJsonPath('data.*.name', ['Amal', 'Bassem']);
        $this->getJson('/api/supervisors?filter[status]=inactive')->assertOk()->assertJsonPath('data.*.name', ['Bassem']);
        $this->getJson('/api/supervisors?filter[email]=corp')->assertOk()->assertJsonPath('data.*.name', ['Amal']);
        $this->getJson('/api/supervisors?filter[status]=gone')->assertUnprocessable();
    });

    it('deletes a supervisor', function () {
        $supervisor = Supervisor::factory()->create();

        $this->deleteJson("/api/supervisors/{$supervisor->id}")->assertNoContent();

        expect(Supervisor::count())->toBe(0);
    });
});

describe('permissions', function () {
    it('lets HR read but not change supervisors', function () {
        actingAsRole(RoleName::Hr);
        $supervisor = Supervisor::factory()->create();

        $this->getJson('/api/supervisors')->assertOk();
        $this->postJson('/api/supervisors', supervisorPayload())->assertForbidden();
        $this->putJson("/api/supervisors/{$supervisor->id}", supervisorPayload())->assertForbidden();
        $this->deleteJson("/api/supervisors/{$supervisor->id}")->assertForbidden();
    });

    it('keeps supervisors away from managers and employees', function (RoleName $role) {
        actingAsRole($role);

        $this->getJson('/api/supervisors')->assertForbidden();
    })->with([RoleName::Manager, RoleName::Employee]);
});
