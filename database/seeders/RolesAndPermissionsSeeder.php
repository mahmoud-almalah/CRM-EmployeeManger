<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Shared\Enums\RoleName;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Permissions are named "<resource>.<action>". The matrix below is the single
 * place that says what each role may do; running the seeder again re-syncs it.
 */
class RolesAndPermissionsSeeder extends Seeder
{
    public const GUARD = 'api';

    private const CRUD = ['view', 'create', 'update', 'delete'];

    private const RESOURCES = [
        'users', 'countries', 'cities', 'positions', 'work-areas', 'work-shifts', 'supervisors',
        'holidays', 'attendance', 'salary-increments', 'payments', 'expenses', 'meetings',
        'notifications', 'insurances', 'terminations',
    ];

    private const EXTRA = [
        'attendance.check', 'salaries.view', 'payroll.generate', 'terminations.approve', 'meetings.respond',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->allPermissions() as $name) {
            Permission::findOrCreate($name, self::GUARD);
        }

        foreach ($this->matrix() as $role => $permissions) {
            Role::findOrCreate($role, self::GUARD)->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return array<int, string>
     */
    public function allPermissions(): array
    {
        $crud = [];

        foreach (self::RESOURCES as $resource) {
            foreach (self::CRUD as $action) {
                $crud[] = "$resource.$action";
            }
        }

        return [...$crud, ...self::EXTRA];
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function matrix(): array
    {
        return [
            RoleName::Admin->value => $this->allPermissions(),

            RoleName::Hr->value => [
                ...$this->crud('positions', 'holidays', 'salary-increments', 'payments', 'insurances'),
                'users.view', 'users.create', 'users.update',
                ...$this->only('view', 'countries', 'cities', 'work-areas', 'work-shifts', 'supervisors', 'expenses'),
                'attendance.view', 'attendance.create', 'attendance.update',
                'terminations.view', 'terminations.create', 'terminations.update', 'terminations.approve',
                'meetings.view', 'meetings.create', 'meetings.respond',
                'notifications.view', 'notifications.create', 'notifications.update',
                'salaries.view', 'payroll.generate', 'attendance.check',
            ],

            RoleName::Manager->value => [
                ...$this->crud('work-shifts', 'expenses', 'meetings'),
                ...$this->only('view', 'users', 'work-areas', 'positions', 'holidays', 'attendance', 'terminations'),
                'terminations.create',
                'meetings.respond', 'attendance.check',
                'notifications.view', 'notifications.update',
            ],

            RoleName::Employee->value => [
                'users.view', 'attendance.view', 'attendance.check', 'holidays.view',
                'meetings.view', 'meetings.respond',
                'notifications.view', 'notifications.update',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    private function crud(string ...$resources): array
    {
        return collect($resources)
            ->flatMap(fn (string $resource) => array_map(fn (string $action) => "$resource.$action", self::CRUD))
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function only(string $action, string ...$resources): array
    {
        return array_map(fn (string $resource) => "$resource.$action", $resources);
    }
}
