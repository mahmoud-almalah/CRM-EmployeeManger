<?php

use Domain\User\Entities\User;

// Identifier quoting differs per driver ("x" on SQLite, `x` on MySQL), so the
// SQL is compared without quote characters.
function sqlOf($query): string
{
    return str_replace(['"', '`'], '', $query->toSql());
}

it('filters inactive users on the status column', function () {
    $query = User::query()->inactive();

    expect(sqlOf($query))->toContain('status = ?')
        ->and($query->getBindings())->toBe(['inactive']);
});

it('keeps the not-terminated condition grouped when chained', function () {
    $query = User::query()->remote()->notTerminated();

    expect(sqlOf($query))->toContain('is_remote = ? and (status != ?)')
        ->and($query->getBindings())->toBe([true, 'terminated']);
});

it('requires an end date for terminated users', function () {
    expect(sqlOf(User::query()->terminated()))->toContain('end_working_date is not null');
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
