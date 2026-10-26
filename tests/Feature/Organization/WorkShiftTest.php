<?php

use Domain\User\Entities\User;
use Domain\WorkArea\Entities\WorkArea;
use Domain\WorkShift\Entities\WorkShift;
use Shared\Enums\RoleName;

function shiftPayload(array $overrides = []): array
{
    return array_merge(['name' => 'morning', 'start_time' => '06:00', 'end_time' => '14:00', 'description' => 'Early'], $overrides);
}

describe('as manager', function () {
    beforeEach(function () {
        actingAsRole(RoleName::Manager);
        $this->area = WorkArea::factory()->create();
        $this->url = "/api/work-areas/{$this->area->id}/shifts";
    });

    it('creates a shift for the work area', function () {
        $this->postJson($this->url, shiftPayload())
            ->assertCreated()
            ->assertJsonPath('data.work_area_id', $this->area->id)
            ->assertJsonPath('data.name', 'morning')
            ->assertJsonPath('data.start_time', '06:00')
            ->assertJsonPath('data.end_time', '14:00')
            ->assertJsonPath('data.is_overnight', false);

        expect($this->area->shifts()->count())->toBe(1);
    });

    it('allows a shift that runs past midnight', function () {
        $this->postJson($this->url, shiftPayload(['name' => 'night', 'start_time' => '22:00', 'end_time' => '06:00']))
            ->assertCreated()
            ->assertJsonPath('data.is_overnight', true);
    });

    it('rejects equal times, bad times and unknown names', function (array $overrides, string $field) {
        $this->postJson($this->url, shiftPayload($overrides))
            ->assertUnprocessable()
            ->assertJsonValidationErrors($field);
    })->with([
        'same start and end' => [['start_time' => '09:00', 'end_time' => '09:00'], 'end_time'],
        'seconds are not accepted' => [['start_time' => '09:00:00'], 'start_time'],
        'invalid hour' => [['end_time' => '25:00'], 'end_time'],
        'unknown name' => [['name' => 'graveyard'], 'name'],
        'missing start' => [['start_time' => null], 'start_time'],
    ]);

    it('refuses a shift that overlaps another one of the same work area', function () {
        WorkShift::factory()->morning()->create(['work_area_id' => $this->area->id]);

        $this->postJson($this->url, shiftPayload(['name' => 'afternoon', 'start_time' => '13:00', 'end_time' => '21:00']))
            ->assertStatus(409)
            ->assertJsonPath('message', 'The shift overlaps the morning shift (06:00-14:00) of the same work area.');

        expect($this->area->shifts()->count())->toBe(1);
    });

    it('accepts shifts that touch but do not overlap, including an overnight one', function () {
        $this->postJson($this->url, shiftPayload())->assertCreated();
        $this->postJson($this->url, shiftPayload(['name' => 'afternoon', 'start_time' => '14:00', 'end_time' => '22:00']))->assertCreated();
        $this->postJson($this->url, shiftPayload(['name' => 'night', 'start_time' => '22:00', 'end_time' => '06:00']))->assertCreated();

        expect($this->area->shifts()->count())->toBe(3);
    });

    it('lets two work areas use the same hours', function () {
        $other = WorkArea::factory()->create();
        WorkShift::factory()->morning()->create(['work_area_id' => $other->id]);

        $this->postJson($this->url, shiftPayload())->assertCreated();
    });

    it('lists the shifts of one work area, earliest first, and filters by name', function () {
        WorkShift::factory()->night()->create(['work_area_id' => $this->area->id]);
        WorkShift::factory()->morning()->create(['work_area_id' => $this->area->id]);
        WorkShift::factory()->create();

        $this->getJson($this->url)
            ->assertOk()
            ->assertJsonPath('data.*.name', ['morning', 'night'])
            ->assertJsonPath('meta.total', 2);

        $this->getJson($this->url.'?filter[name]=night')->assertOk()->assertJsonPath('data.*.name', ['night']);
    });

    it('shows a shift only through its own work area', function () {
        $shift = WorkShift::factory()->create(['work_area_id' => $this->area->id]);
        $other = WorkArea::factory()->create();

        $this->getJson("{$this->url}/{$shift->id}")->assertOk()->assertJsonPath('data.id', $shift->id);
        $this->getJson("/api/work-areas/{$other->id}/shifts/{$shift->id}")->assertNotFound();
        $this->getJson("{$this->url}/999")->assertNotFound();
        $this->getJson('/api/work-areas/999/shifts')->assertNotFound();
    });

    it('updates a shift without clashing with itself', function () {
        $shift = WorkShift::factory()->morning()->create(['work_area_id' => $this->area->id]);

        $this->putJson("{$this->url}/{$shift->id}", shiftPayload(['start_time' => '07:00', 'end_time' => '15:00']))
            ->assertOk()
            ->assertJsonPath('data.start_time', '07:00');

        expect($shift->fresh()->start_time)->toStartWith('07:00');
    });

    it('refuses an update that would overlap a sibling', function () {
        WorkShift::factory()->afternoon()->create(['work_area_id' => $this->area->id]);
        $shift = WorkShift::factory()->morning()->create(['work_area_id' => $this->area->id]);

        $this->putJson("{$this->url}/{$shift->id}", shiftPayload(['end_time' => '15:00']))->assertStatus(409);
    });

    it('does not move a shift to another work area', function () {
        $other = WorkArea::factory()->create();
        $shift = WorkShift::factory()->create(['work_area_id' => $other->id]);

        $this->putJson("{$this->url}/{$shift->id}", shiftPayload())->assertNotFound();
    });

    it('deletes a shift unless employees are assigned to it', function () {
        $free = WorkShift::factory()->morning()->create(['work_area_id' => $this->area->id]);
        $busy = WorkShift::factory()->afternoon()->create(['work_area_id' => $this->area->id]);
        User::factory()->create(['work_shift_id' => $busy->id]);

        $this->deleteJson("{$this->url}/{$free->id}")->assertNoContent();
        $this->deleteJson("{$this->url}/{$busy->id}")->assertStatus(409);

        expect($this->area->shifts()->count())->toBe(1);
    });

    it('includes the shifts when a work area is requested', function () {
        WorkShift::factory()->morning()->create(['work_area_id' => $this->area->id]);

        $this->getJson("/api/work-areas/{$this->area->id}?include=shifts")
            ->assertOk()
            ->assertJsonPath('data.shifts.0.name', 'morning');
    });
});

describe('permissions', function () {
    it('lets HR read but not change shifts', function () {
        actingAsRole(RoleName::Hr);
        $shift = WorkShift::factory()->create();
        $url = "/api/work-areas/{$shift->work_area_id}/shifts";

        $this->getJson($url)->assertOk();
        $this->postJson($url, shiftPayload())->assertForbidden();
        $this->putJson("{$url}/{$shift->id}", shiftPayload())->assertForbidden();
        $this->deleteJson("{$url}/{$shift->id}")->assertForbidden();
    });

    it('keeps shifts away from employees', function () {
        actingAsRole(RoleName::Employee);
        $shift = WorkShift::factory()->create();

        $this->getJson("/api/work-areas/{$shift->work_area_id}/shifts")->assertForbidden();
    });
});
