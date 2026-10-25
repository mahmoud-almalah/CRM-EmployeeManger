<?php

use Domain\User\Entities\User;
use Domain\User\ValueObjects\EmailObject;
use Domain\User\ValueObjects\NameObject;
use Domain\User\ValueObjects\PhoneObject;
use Illuminate\Support\Facades\DB;
use Shared\Enums\UserStatus;

function newUser(array $overrides = []): User
{
    return User::create(array_merge([
        'name' => new NameObject('Mahmoud', null, 'Almalah'),
        'username' => 'mahmoud',
        'email' => new EmailObject('Mahmoud@example.com'),
        'phone_number' => new PhoneObject('1012345678', '+20'),
        'birth_date' => '1995-01-01',
        'start_working_date' => '2024-01-01',
        'salary' => 1000,
        'password' => 'secret-password',
    ], $overrides));
}

it('stores value objects and reads them back unchanged', function () {
    $id = newUser()->id;

    $user = User::findOrFail($id);

    expect($user->name->fullName())->toBe('Mahmoud Almalah')
        ->and($user->email->getEmail())->toBe('mahmoud@example.com')
        ->and($user->phone_number->getFullNumber())->toBe('+201012345678')
        ->and($user->status)->toBe(UserStatus::Active);
});

it('keeps email and phone as searchable plain strings', function () {
    newUser();

    $row = DB::table('users')->first();

    expect($row->email)->toBe('mahmoud@example.com')
        ->and($row->phone_number)->toBe('+20 1012345678')
        ->and(json_decode($row->name, true))->toBe(['first' => 'Mahmoud', 'middle' => null, 'last' => 'Almalah']);
});

it('hashes the password', function () {
    $user = newUser();

    expect($user->password)->not->toBe('secret-password')
        ->and(Hash::check('secret-password', $user->password))->toBeTrue();
});
