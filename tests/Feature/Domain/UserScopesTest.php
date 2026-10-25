<?php

use Domain\User\Entities\User;

it('filters inactive users on the status column', function () {
    $query = User::query()->inactive();

    expect($query->toSql())->toContain('"status" = ?')
        ->and($query->getBindings())->toBe(['inactive']);
});

it('keeps the not-terminated condition grouped when chained', function () {
    $query = User::query()->remote()->notTerminated();

    expect($query->toSql())->toBe('select * from "users" where "is_remote" = ? and ("status" != ?)')
        ->and($query->getBindings())->toBe([true, 'terminated']);
});

it('requires an end date for terminated users', function () {
    expect(User::query()->terminated()->toSql())->toContain('"end_working_date" is not null');
});
