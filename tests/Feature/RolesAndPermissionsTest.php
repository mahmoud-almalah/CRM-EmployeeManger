<?php

use Database\Seeders\RolesAndPermissionsSeeder;
use Domain\User\Entities\User;
use Shared\Enums\RoleName;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(fn () => $this->seed(RolesAndPermissionsSeeder::class));

function userWithRole(RoleName $role): User
{
    return User::factory()->create()->assignRole($role->value);
}

it('creates the four roles on the api guard', function () {
    expect(Role::pluck('name')->sort()->values()->all())->toBe(['admin', 'employee', 'hr', 'manager'])
        ->and(Role::where('guard_name', '!=', 'api')->count())->toBe(0);
});

it('names permissions as resource.action', function () {
    expect(Permission::pluck('name')->every(fn (string $name) => preg_match('/^[a-z-]+\.[a-z-]+$/', $name) === 1))->toBeTrue();
});

it('is idempotent', function () {
    $permissions = Permission::count();
    $roles = Role::count();

    $this->seed(RolesAndPermissionsSeeder::class);

    expect(Permission::count())->toBe($permissions)->and(Role::count())->toBe($roles);
});

it('gives the admin every permission', function () {
    expect(Role::findByName('admin', 'api')->permissions)->toHaveCount(Permission::count());
});

it('lets HR manage people and money but not other roles', function () {
    $hr = userWithRole(RoleName::Hr);

    expect($hr->can('users.create'))->toBeTrue()
        ->and($hr->can('payments.create'))->toBeTrue()
        ->and($hr->can('payroll.generate'))->toBeTrue()
        ->and($hr->can('work-areas.create'))->toBeFalse();
});

it('keeps money out of the manager and employee roles', function () {
    $manager = userWithRole(RoleName::Manager);
    $employee = userWithRole(RoleName::Employee);

    expect($manager->can('payroll.generate'))->toBeFalse()
        ->and($manager->can('salaries.view'))->toBeFalse()
        ->and($manager->can('meetings.create'))->toBeTrue()
        ->and($employee->can('payments.view'))->toBeFalse()
        ->and($employee->can('users.create'))->toBeFalse()
        ->and($employee->can('attendance.check'))->toBeTrue();
});

it('removes a permission from a role when the matrix drops it', function () {
    Role::findByName('employee', 'api')->givePermissionTo('payments.view');

    $this->seed(RolesAndPermissionsSeeder::class);

    expect(userWithRole(RoleName::Employee)->can('payments.view'))->toBeFalse();
});

it('keeps deleting accounts for admins only', function () {
    expect(userWithRole(RoleName::Hr)->can('users.delete'))->toBeFalse()
        ->and(userWithRole(RoleName::Admin)->can('users.delete'))->toBeTrue();
});
