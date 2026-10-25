<?php

use Shared\Enums\UserStatus;

it('exposes the user statuses as plain values', function () {
    expect(UserStatus::values())->toBe(['active', 'inactive', 'terminated']);
});
