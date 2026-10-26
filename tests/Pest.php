<?php

use Database\Seeders\RolesAndPermissionsSeeder;
use Domain\City\Entities\City;
use Domain\Country\Entities\Country;
use Domain\Position\Entities\Position;
use Domain\User\Entities\User;
use Domain\WorkArea\Entities\WorkArea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Passport;
use Shared\Enums\RoleName;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test suites
|--------------------------------------------------------------------------
|
| Unit tests are plain PHP and never touch the database or the container.
| Feature tests boot the application and run against a fresh in-memory
| SQLite database, so they never depend on a local MySQL server.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * Passport needs signing keys and a personal access client before it can
 * issue a token. Both are created here so tests do not depend on a local setup.
 */
function setUpPassport(): void
{
    Artisan::call('passport:keys');
    Artisan::call('passport:client', [
        '--personal' => true,
        '--name' => 'Testing personal access client',
        '--provider' => 'users',
    ]);
}

/**
 * The reference rows a user points at, built with the organization factories.
 * The shift is still inserted directly until the WorkShift factory exists.
 *
 * @return array{country_id: int, city_id: int, position_id: int, work_area_id: int, work_shift_id: int}
 */
function organization(): array
{
    $country = Country::factory()->create();
    $city = City::factory()->create(['country_id' => $country->id]);
    $position = Position::factory()->create();
    $workArea = WorkArea::factory()->create(['city_id' => $city->id, 'country_id' => $country->id]);
    $shiftId = DB::table('work_shifts')->insertGetId([
        'work_area_id' => $workArea->id, 'name' => 'morning', 'start_time' => '09:00', 'end_time' => '17:00',
        'created_at' => now(), 'updated_at' => now(),
    ]);

    return [
        'country_id' => $country->id, 'city_id' => $city->id, 'position_id' => $position->id,
        'work_area_id' => $workArea->id, 'work_shift_id' => $shiftId,
    ];
}

/**
 * A request body that passes CreateUserRequest.
 *
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function userPayload(array $overrides = []): array
{
    return array_merge(organization(), [
        'name' => ['first' => 'Sara', 'last' => 'Hassan'],
        'username' => 'sara.hassan',
        'email' => 'Sara@Example.com',
        'phone' => ['country_code' => '+20', 'number' => '1098765432'],
        'birth_date' => '1996-05-10',
        'start_working_date' => '2025-01-01',
        'salary' => 2500,
        'role' => 'employee',
        'password' => 'a-strong-password',
    ], $overrides);
}

/**
 * Signs in as a user holding the given role.
 */
function actingAsRole(RoleName $role): User
{
    test()->seed(RolesAndPermissionsSeeder::class);

    $user = User::factory()->create()->assignRole($role->value);
    Passport::actingAs($user, [], 'api');

    return $user;
}
