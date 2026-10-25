<?php

declare(strict_types=1);

namespace Application\Providers;

use Application\Bus\Command\IlluminateCommandBus;
use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Bus\Query\IlluminateQueryBus;
use Application\User\Contracts\UserServiceContract;
use Application\User\Queries\GetAllUsersQuery;
use Application\User\QueriesHandlers\GetAllUsersQueryHandler;
use Application\User\Services\UserService;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    public $singletons = [
        CommandBusContract::class => IlluminateCommandBus::class,
        QueryBusContract::class => IlluminateQueryBus::class,
    ];

    public $bindings = [
        UserServiceContract::class => UserService::class,
    ];

    public function boot(): void
    {
        $this->registerCommandHandlers();
        $this->registerQueryHandlers();
    }

    protected function registerCommandHandlers(): void
    {
        $commandBus = app(CommandBusContract::class);
        $commandBus->register([

        ]);
    }

    protected function registerQueryHandlers(): void
    {
        $queryBus = app(QueryBusContract::class);
        $queryBus->register([
            GetAllUsersQuery::class => GetAllUsersQueryHandler::class,
        ]);
    }
}
