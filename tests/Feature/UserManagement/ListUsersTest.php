<?php

use Domain\User\Entities\User;
use Domain\User\ValueObjects\EmailObject;
use Domain\User\ValueObjects\NameObject;
use Laravel\Passport\Passport;
use Shared\Enums\RoleName;

beforeEach(function () {
    actingAsRole(RoleName::Hr);
});

function usernames($response): array
{
    return collect($response->json('data'))->pluck('username')->all();
}

it('lists users newest first with pagination meta', function () {
    User::factory()->count(20)->create();

    $this->getJson('/api/users?per_page=10&page=2')
        ->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.current_page', 2)
        ->assertJsonPath('meta.per_page', 10)
        ->assertJsonPath('meta.total', 21);
});

it('filters by status', function () {
    User::factory()->count(2)->create();
    User::factory()->inactive()->create(['username' => 'paused']);
    User::factory()->terminated()->create(['username' => 'gone']);

    expect(usernames($this->getJson('/api/users?filter[status]=inactive')))->toBe(['paused'])
        ->and(usernames($this->getJson('/api/users?filter[status]=terminated')))->toBe(['gone']);
});

it('filters by remote and freelance flags', function () {
    User::factory()->create(['username' => 'office']);
    User::factory()->remote()->create(['username' => 'home']);
    User::factory()->freelance()->create(['username' => 'contractor']);

    expect(usernames($this->getJson('/api/users?filter[is_remote]=true')))->toBe(['home'])
        ->and(usernames($this->getJson('/api/users?filter[is_freelance]=true')))->toBe(['contractor']);
});

it('filters by position and work area', function () {
    $org = organization();
    User::factory()->create(['username' => 'dev', 'position_id' => $org['position_id'], 'work_area_id' => $org['work_area_id']]);
    User::factory()->create(['username' => 'other']);

    expect(usernames($this->getJson("/api/users?filter[position_id]={$org['position_id']}")))->toBe(['dev'])
        ->and(usernames($this->getJson("/api/users?filter[work_area_id]={$org['work_area_id']}")))->toBe(['dev']);
});

it('searches username, email and name', function () {
    User::factory()->create(['username' => 'findme']);
    User::factory()->create(['username' => 'a', 'name' => new NameObject('Zainab', null, 'Farouk')]);
    User::factory()->create(['username' => 'b', 'email' => new EmailObject('unique-address@example.com')]);

    expect(usernames($this->getJson('/api/users?filter[search]=findme')))->toBe(['findme'])
        ->and(usernames($this->getJson('/api/users?filter[search]=zainab')))->toBe(['a'])
        ->and(usernames($this->getJson('/api/users?filter[search]=unique-address')))->toBe(['b']);
});

it('treats wildcards in the search term literally', function () {
    User::factory()->count(3)->create();

    $this->getJson('/api/users?filter[search]=%25')->assertOk()->assertJsonCount(0, 'data');
});

it('sorts by allowed columns in both directions', function () {
    User::factory()->create(['username' => 'bbb', 'salary' => 100]);
    User::factory()->create(['username' => 'aaa', 'salary' => 300]);
    User::factory()->create(['username' => 'ccc', 'salary' => 200]);

    $names = fn (string $sort) => array_values(array_filter(
        usernames($this->getJson("/api/users?sort=$sort")),
        fn ($name) => in_array($name, ['aaa', 'bbb', 'ccc']),
    ));

    expect($names('username'))->toBe(['aaa', 'bbb', 'ccc'])
        ->and($names('-username'))->toBe(['ccc', 'bbb', 'aaa'])
        ->and($names('-salary'))->toBe(['aaa', 'ccc', 'bbb']);
});

it('rejects filters, sorts and includes that are not allowed', function () {
    $this->getJson('/api/users?filter[password]=x')->assertStatus(400);
    $this->getJson('/api/users?sort=password')->assertStatus(400);
    $this->getJson('/api/users?include=tokens')->assertStatus(400);
});

it('validates the paging and filter values', function () {
    $this->getJson('/api/users?per_page=500&filter[status]=nope')
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['per_page', 'filter.status']);
});

it('includes roles without extra queries per user', function () {
    User::factory()->count(5)->create()->each->assignRole('employee');

    $this->getJson('/api/users?include=roles')->assertOk();
    $queries = 0;
    DB::listen(function () use (&$queries) {
        $queries++;
    });

    $this->getJson('/api/users?include=roles&per_page=50')->assertOk();

    expect($queries)->toBeLessThanOrEqual(8);
});

it('requires the users.view permission', function () {
    actingAsRole(RoleName::Employee);
    $this->getJson('/api/users')->assertOk();

    Passport::actingAs(User::factory()->create(), [], 'api');
    $this->getJson('/api/users')->assertForbidden();
});
