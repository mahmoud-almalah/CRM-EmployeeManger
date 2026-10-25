<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

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

pest()->extend(Tests\TestCase::class)
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
 * The reference rows a user points at. Sprint 2 adds proper factories for these;
 * until then they are inserted directly.
 *
 * @return array{country_id: int, city_id: int, position_id: int, work_area_id: int, work_shift_id: int}
 */
function organization(): array
{
    $now = now();

    $countryId = DB::table('countries')->insertGetId(['name' => 'Egypt', 'phone_code' => '+20', 'currency' => 'EGP', 'created_at' => $now, 'updated_at' => $now]);
    $cityId = DB::table('cities')->insertGetId(['country_id' => $countryId, 'name' => 'Cairo', 'created_at' => $now, 'updated_at' => $now]);
    $positionId = DB::table('positions')->insertGetId(['name' => 'Developer', 'created_at' => $now, 'updated_at' => $now]);
    $managerId = Domain\User\Entities\User::factory()->create()->id;
    $workAreaId = DB::table('work_areas')->insertGetId([
        'name' => 'Head office', 'address' => '1 Main St', 'city' => $cityId, 'country' => $countryId,
        'manager_id' => $managerId, 'created_at' => $now, 'updated_at' => $now,
    ]);
    $shiftId = DB::table('work_shifts')->insertGetId([
        'work_area_id' => $workAreaId, 'name' => 'morning', 'start_time' => '09:00', 'end_time' => '17:00',
        'created_at' => $now, 'updated_at' => $now,
    ]);

    return [
        'country_id' => $countryId, 'city_id' => $cityId, 'position_id' => $positionId,
        'work_area_id' => $workAreaId, 'work_shift_id' => $shiftId,
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
function actingAsRole(Shared\Enums\RoleName $role): Domain\User\Entities\User
{
    test()->seed(Database\Seeders\RolesAndPermissionsSeeder::class);

    $user = Domain\User\Entities\User::factory()->create()->assignRole($role->value);
    Laravel\Passport\Passport::actingAs($user, [], 'api');

    return $user;
}
