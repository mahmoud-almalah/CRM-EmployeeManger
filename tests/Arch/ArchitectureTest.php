<?php

declare(strict_types=1);
use Application\Bus\Command\Command;
use Application\Bus\Command\CommandHandler;
use Application\Bus\Query\Query;
use Application\Bus\Query\QueryHandler;
use Application\User\QueriesHandlers\UserIncludes;

/*
|--------------------------------------------------------------------------
| Architecture rules
|--------------------------------------------------------------------------
|
| docs/architecture.md says dependencies point inward:
| Presentation -> Application -> Domain, with Infrastructure implementing
| the contracts Application defines. These tests make the build fail when
| that stops being true, instead of relying on review.
|
*/

arch('domain does not know the outer layers')
    ->expect('Domain')
    ->not->toUse(['Application', 'Infrastructure', 'Presentation']);

arch('domain only depends on itself, shared code and the framework')
    ->expect('Domain')
    ->toOnlyUse(['Domain', 'Shared', 'Illuminate', 'Laravel\Passport', 'Spatie\Permission', 'Database\Factories']);

arch('application does not know infrastructure or presentation')
    ->expect('Application')
    ->not->toUse(['Infrastructure', 'Presentation']);

arch('infrastructure does not know presentation')
    ->expect('Infrastructure')
    ->not->toUse('Presentation');

arch('presentation reaches the application through the buses, never through infrastructure')
    ->expect('Presentation')
    ->not->toUse('Infrastructure');

arch('shared code depends on no layer')
    ->expect('Shared')
    ->not->toUse(['Domain', 'Application', 'Infrastructure', 'Presentation']);

/*
 | Every module under src/Application follows the same slice, so the
 | conventions are checked once per module found on disk. A new module is
 | covered without touching this file.
 */
$modules = array_map('basename', glob(dirname(__DIR__, 2).'/src/Application/*', GLOB_ONLYDIR) ?: []);

foreach (array_diff($modules, ['Bus']) as $module) {
    if (is_dir(dirname(__DIR__, 2)."/src/Application/{$module}/CommandHandlers")) {
        arch("{$module} command handlers extend the command handler base class")
            ->expect("Application\\{$module}\\CommandHandlers")
            ->toExtend(CommandHandler::class);
    }

    if (is_dir(dirname(__DIR__, 2)."/src/Application/{$module}/QueriesHandlers")) {
        arch("{$module} query handlers extend the query handler base class")
            ->expect("Application\\{$module}\\QueriesHandlers")
            ->toExtend(QueryHandler::class)
            ->ignoring(UserIncludes::class);
    }

    if (is_dir(dirname(__DIR__, 2)."/src/Application/{$module}/Commands")) {
        arch("{$module} commands extend the command base class")
            ->expect("Application\\{$module}\\Commands")
            ->toExtend(Command::class);
    }

    if (is_dir(dirname(__DIR__, 2)."/src/Application/{$module}/Queries")) {
        arch("{$module} queries extend the query base class")
            ->expect("Application\\{$module}\\Queries")
            ->toExtend(Query::class);
    }
}

/*
 | Controllers translate HTTP into a command or query and back. They never
 | touch the database. Form requests are not included: they run uniqueness
 | lookups with DB::table() while validating, which is tracked as tech debt.
 */
$presentation = array_map('basename', glob(dirname(__DIR__, 2).'/src/Presentation/*', GLOB_ONLYDIR) ?: []);

foreach ($presentation as $module) {
    if (is_dir(dirname(__DIR__, 2)."/src/Presentation/{$module}/Controllers")) {
        arch("{$module} controllers do not query the database")
            ->expect("Presentation\\{$module}\\Controllers")
            ->not->toUse(['Illuminate\Database', 'Illuminate\Support\Facades\DB']);
    }
}
