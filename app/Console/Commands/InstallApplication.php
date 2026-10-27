<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Database\Seeders\DatabaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Console\Command;
use Laravel\Passport\Client;

/**
 * Brings an empty environment to the point where the API can serve requests.
 * Safe to run on every start: each step does nothing when it is already done.
 */
final class InstallApplication extends Command
{
    protected $signature = 'app:install {--demo : Also load demo employees with a known admin password (never use in production)}';

    protected $description = 'Run migrations, create the Passport keys and client, and seed roles and permissions';

    public function handle(): int
    {
        $this->call('migrate', ['--force' => true]);

        $this->ensurePassportKeys();
        $this->ensurePersonalAccessClient();

        // The seeder is idempotent, so roles and permissions are always brought up to date.
        $this->call('db:seed', ['--class' => RolesAndPermissionsSeeder::class, '--force' => true]);

        if ($this->option('demo')) {
            $this->warn('Loading demo data with the admin password "password".');
            $this->call('db:seed', ['--class' => DatabaseSeeder::class, '--force' => true]);
        }

        $this->info('The application is installed.');

        return self::SUCCESS;
    }

    private function ensurePassportKeys(): void
    {
        $fromEnvironment = config('passport.private_key') !== null && config('passport.public_key') !== null;
        $onDisk = is_file(storage_path('oauth-private.key')) && is_file(storage_path('oauth-public.key'));

        if ($fromEnvironment || $onDisk) {
            return;
        }

        $this->call('passport:keys');
    }

    private function ensurePersonalAccessClient(): void
    {
        if (Client::query()->where('personal_access_client', true)->exists()) {
            return;
        }

        $this->call('passport:client', ['--personal' => true, '--name' => 'Personal access client', '--provider' => 'users']);
    }
}
