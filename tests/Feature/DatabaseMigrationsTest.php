<?php

use Illuminate\Support\Facades\Schema;

it('runs every migration on a fresh database', function () {
    foreach (['users', 'attendance_records', 'payments', 'work_areas', 'work_shifts', 'meetings'] as $table) {
        expect(Schema::hasTable($table))->toBeTrue("Missing table [$table]");
    }
});
