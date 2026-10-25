<?php

use Domain\User\Entities\User;
use Shared\Enums\UserStatus;

it('creates valid, distinct users', function () {
    $users = User::factory()->count(5)->create();

    expect($users)->toHaveCount(5)
        ->and($users->pluck('username')->unique())->toHaveCount(5)
        ->and($users->first()->email->getEmail())->toContain('@');
});

it('has states for the cases the domain cares about', function () {
    $terminated = User::factory()->terminated()->create();

    expect($terminated->status)->toBe(UserStatus::Terminated)
        ->and($terminated->end_working_date)->not->toBeNull()
        ->and(User::factory()->freelance()->create()->is_freelance)->toBeTrue();
});

it('seeds the database', function () {
    $this->seed();

    expect(User::where('username', 'admin')->exists())->toBeTrue()
        ->and(User::count())->toBe(14);
});
