<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

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
