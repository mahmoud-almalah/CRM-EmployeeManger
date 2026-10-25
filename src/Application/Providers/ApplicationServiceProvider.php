<?php

declare(strict_types=1);

namespace Application\Providers;

use Application\Auth\CommandHandlers\LoginCommandHandler;
use Application\Auth\CommandHandlers\LogoutCommandHandler;
use Application\Auth\Commands\LoginCommand;
use Application\Auth\Commands\LogoutCommand;
use Application\Bus\Command\IlluminateCommandBus;
use Application\Bus\Contracts\CommandBusContract;
use Application\Bus\Contracts\QueryBusContract;
use Application\Bus\Query\IlluminateQueryBus;
use Application\User\CommandHandlers\CreateUserCommandHandler;
use Application\User\Commands\CreateUserCommand;
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
        $this->app->make(CommandBusContract::class)->register([
            LoginCommand::class => LoginCommandHandler::class,
            LogoutCommand::class => LogoutCommandHandler::class,
            CreateUserCommand::class => CreateUserCommandHandler::class,
        ]);
    }

    protected function registerQueryHandlers(): void
    {
        $this->app->make(QueryBusContract::class)->register([
            GetAllUsersQuery::class => GetAllUsersQueryHandler::class,
        ]);
    }
}
