<?php

use Domain\Position\Entities\Position;
use Domain\User\Entities\User;
use Shared\Enums\RoleName;

function positionPayload(array $overrides = []): array
{
    return array_merge(['name' => 'Backend developer', 'description' => 'Builds the API'], $overrides);
}

describe('as HR', function () {
    beforeEach(function () {
        actingAsRole(RoleName::Hr);
    });

    it('creates a position', function () {
        $this->postJson('/api/positions', positionPayload())
            ->assertCreated()
            ->assertJsonPath('data.name', 'Backend developer')
            ->assertJsonPath('data.description', 'Builds the API');

        expect(Position::count())->toBe(1);
    });

    it('needs a name and keeps it unique', function () {
        $this->postJson('/api/positions', positionPayload(['name' => '']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        Position::factory()->create(['name' => 'Designer']);

        $this->postJson('/api/positions', positionPayload(['name' => 'Designer']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    });

    it('shows a position and answers 404 for an unknown one', function () {
        $position = Position::factory()->create();

        $this->getJson("/api/positions/{$position->id}")->assertOk()->assertJsonPath('data.id', $position->id);
        $this->getJson('/api/positions/999')->assertNotFound();
        $this->putJson('/api/positions/999', [])->assertNotFound();
        $this->deleteJson('/api/positions/999')->assertNotFound();
    });

    it('updates a position, which may keep its own name', function () {
        $position = Position::factory()->create(['name' => 'Designer']);

        $this->putJson("/api/positions/{$position->id}", positionPayload(['name' => 'Designer', 'description' => 'UI']))
            ->assertOk()
            ->assertJsonPath('data.description', 'UI');

        $other = Position::factory()->create(['name' => 'Tester']);

        $this->putJson("/api/positions/{$other->id}", positionPayload(['name' => 'Designer']))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    });

    it('lists and filters positions', function () {
        Position::factory()->create(['name' => 'Designer']);
        Position::factory()->create(['name' => 'Developer']);
        Position::factory()->create(['name' => 'Tester']);

        $this->getJson('/api/positions')->assertOk()->assertJsonPath('data.*.name', ['Designer', 'Developer', 'Tester']);
        $this->getJson('/api/positions?filter[name]=dev&sort=-name')->assertOk()->assertJsonPath('data.*.name', ['Developer']);
    });

    it('deletes an unused position and refuses one that users hold', function () {
        $free = Position::factory()->create();
        $held = Position::factory()->create();
        User::factory()->create(['position_id' => $held->id]);

        $this->deleteJson("/api/positions/{$free->id}")->assertNoContent();
        $this->deleteJson("/api/positions/{$held->id}")->assertStatus(409);

        expect(Position::count())->toBe(1);
    });
});

describe('permissions', function () {
    it('lets managers read but not change positions', function () {
        actingAsRole(RoleName::Manager);
        $position = Position::factory()->create();

        $this->getJson('/api/positions')->assertOk();
        $this->postJson('/api/positions', positionPayload())->assertForbidden();
        $this->putJson("/api/positions/{$position->id}", positionPayload())->assertForbidden();
        $this->deleteJson("/api/positions/{$position->id}")->assertForbidden();
    });

    it('keeps positions away from employees', function () {
        actingAsRole(RoleName::Employee);

        $this->getJson('/api/positions')->assertForbidden();
    });
});
