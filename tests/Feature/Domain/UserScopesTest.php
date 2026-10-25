<?php

use Domain\User\Entities\User;

it('filters inactive users on the status column', function () {
    $query = User::query()->inactive();

    expect($query->toSql())->toContain('"status" = ?')
        ->and($query->getBindings())->toBe(['inactive']);
});

it('keeps the not-terminated condition grouped when chained', function () {
    $query = User::query()->remote()->notTerminated();

    expect($query->toSql())->toContain('"is_remote" = ? and ("status" != ?)')
        ->and($query->getBindings())->toBe([true, 'terminated']);
});

it('requires an end date for terminated users', function () {
    expect(User::query()->terminated()->toSql())->toContain('"end_working_date" is not null');
});

it('selects the right rows when the scopes run against data', function () {
    User::factory()->count(2)->create();
    User::factory()->remote()->create();
    User::factory()->inactive()->create();
    User::factory()->terminated()->create();

    expect(User::active()->count())->toBe(3)
        ->and(User::inactive()->count())->toBe(1)
        ->and(User::terminated()->count())->toBe(1)
        ->and(User::notTerminated()->count())->toBe(4)
        ->and(User::remote()->notTerminated()->count())->toBe(1);
});
