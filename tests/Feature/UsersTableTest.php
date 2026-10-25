<?php

use Illuminate\Support\Facades\Schema;
use Shared\Enums\UserStatus;

it('stores the status as a string that matches the enum values', function () {
    $status = collect(Schema::getColumns('users'))->firstWhere('name', 'status');

    expect($status['type_name'])->toBe('varchar')
        ->and($status['default'])->toContain(UserStatus::Active->value);
});

it('leaves roles to spatie instead of a role column', function () {
    expect(Schema::hasColumn('users', 'role'))->toBeFalse()
        ->and(Schema::hasTable('roles'))->toBeTrue();
});
