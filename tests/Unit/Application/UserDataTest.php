<?php

use Application\User\Data\UserData;
use Shared\Enums\CurrencyEnum;
use Shared\Enums\UserStatus;

function userInput(array $overrides = []): array
{
    return array_merge([
        'name' => ['first' => 'Mahmoud', 'last' => 'Almalah'],
        'username' => 'mahmoud',
        'email' => 'Mahmoud@Example.com',
        'phone' => ['country_code' => '+20', 'number' => '1012345678'],
        'country_id' => 1,
        'city_id' => 1,
        'birth_date' => '1995-01-01',
        'start_working_date' => '2024-01-01',
        'position_id' => 1,
        'work_area_id' => 1,
        'work_shift_id' => 1,
        'salary' => 1500.5,
        'password' => 'secret-password',
    ], $overrides);
}

it('builds from request-shaped input with sensible defaults', function () {
    $data = UserData::fromArray(userInput());

    expect($data->email->getEmail())->toBe('mahmoud@example.com')
        ->and($data->status)->toBe(UserStatus::Active)
        ->and($data->salaryCurrency)->toBe(CurrencyEnum::USD)
        ->and($data->isRemote)->toBeFalse();
});

it('maps to model attribute names', function () {
    $attributes = UserData::fromArray(userInput())->toArray();

    expect($attributes)->toHaveKeys(['name', 'phone_number', 'country_id', 'start_working_date'])
        ->and($attributes)->not->toHaveKeys(['id', 'role', 'phone']);
});

it('leaves the password out when none is given', function () {
    $attributes = UserData::fromArray(userInput(['password' => null]))->toArray();

    expect($attributes)->not->toHaveKey('password');
});
