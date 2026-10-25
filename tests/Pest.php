<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

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
