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
use Application\City\CommandHandlers\CreateCityCommandHandler;
use Application\City\CommandHandlers\DeleteCityCommandHandler;
use Application\City\CommandHandlers\UpdateCityCommandHandler;
use Application\City\Commands\CreateCityCommand;
use Application\City\Commands\DeleteCityCommand;
use Application\City\Commands\UpdateCityCommand;
use Application\City\Contracts\CityServiceContract;
use Application\City\Queries\GetCitiesQuery;
use Application\City\Queries\GetCityQuery;
use Application\City\QueriesHandlers\GetCitiesQueryHandler;
use Application\City\QueriesHandlers\GetCityQueryHandler;
use Application\City\Services\CityService;
use Application\Country\CommandHandlers\CreateCountryCommandHandler;
use Application\Country\CommandHandlers\DeleteCountryCommandHandler;
use Application\Country\CommandHandlers\UpdateCountryCommandHandler;
use Application\Country\Commands\CreateCountryCommand;
use Application\Country\Commands\DeleteCountryCommand;
use Application\Country\Commands\UpdateCountryCommand;
use Application\Country\Contracts\CountryServiceContract;
use Application\Country\Queries\GetCountriesQuery;
use Application\Country\Queries\GetCountryQuery;
use Application\Country\QueriesHandlers\GetCountriesQueryHandler;
use Application\Country\QueriesHandlers\GetCountryQueryHandler;
use Application\Country\Services\CountryService;
use Application\Health\Queries\GetHealthQuery;
use Application\Health\QueriesHandlers\GetHealthQueryHandler;
use Application\Position\CommandHandlers\CreatePositionCommandHandler;
use Application\Position\CommandHandlers\DeletePositionCommandHandler;
use Application\Position\CommandHandlers\UpdatePositionCommandHandler;
use Application\Position\Commands\CreatePositionCommand;
use Application\Position\Commands\DeletePositionCommand;
use Application\Position\Commands\UpdatePositionCommand;
use Application\Position\Contracts\PositionServiceContract;
use Application\Position\Queries\GetPositionQuery;
use Application\Position\Queries\GetPositionsQuery;
use Application\Position\QueriesHandlers\GetPositionQueryHandler;
use Application\Position\QueriesHandlers\GetPositionsQueryHandler;
use Application\Position\Services\PositionService;
use Application\Supervisor\CommandHandlers\CreateSupervisorCommandHandler;
use Application\Supervisor\CommandHandlers\DeleteSupervisorCommandHandler;
use Application\Supervisor\CommandHandlers\UpdateSupervisorCommandHandler;
use Application\Supervisor\Commands\CreateSupervisorCommand;
use Application\Supervisor\Commands\DeleteSupervisorCommand;
use Application\Supervisor\Commands\UpdateSupervisorCommand;
use Application\Supervisor\Contracts\SupervisorServiceContract;
use Application\Supervisor\Queries\GetSupervisorQuery;
use Application\Supervisor\Queries\GetSupervisorsQuery;
use Application\Supervisor\QueriesHandlers\GetSupervisorQueryHandler;
use Application\Supervisor\QueriesHandlers\GetSupervisorsQueryHandler;
use Application\Supervisor\Services\SupervisorService;
use Application\User\CommandHandlers\CreateUserCommandHandler;
use Application\User\CommandHandlers\DeleteUserCommandHandler;
use Application\User\CommandHandlers\UpdateUserCommandHandler;
use Application\User\Commands\CreateUserCommand;
use Application\User\Commands\DeleteUserCommand;
use Application\User\Commands\UpdateUserCommand;
use Application\User\Contracts\UserServiceContract;
use Application\User\Queries\GetUserQuery;
use Application\User\Queries\GetUsersQuery;
use Application\User\QueriesHandlers\GetUserQueryHandler;
use Application\User\QueriesHandlers\GetUsersQueryHandler;
use Application\User\Services\UserService;
use Application\WorkArea\CommandHandlers\CreateWorkAreaCommandHandler;
use Application\WorkArea\CommandHandlers\DeleteWorkAreaCommandHandler;
use Application\WorkArea\CommandHandlers\UpdateWorkAreaCommandHandler;
use Application\WorkArea\Commands\CreateWorkAreaCommand;
use Application\WorkArea\Commands\DeleteWorkAreaCommand;
use Application\WorkArea\Commands\UpdateWorkAreaCommand;
use Application\WorkArea\Contracts\WorkAreaServiceContract;
use Application\WorkArea\Queries\GetWorkAreaQuery;
use Application\WorkArea\Queries\GetWorkAreasQuery;
use Application\WorkArea\QueriesHandlers\GetWorkAreaQueryHandler;
use Application\WorkArea\QueriesHandlers\GetWorkAreasQueryHandler;
use Application\WorkArea\Services\WorkAreaService;
use Application\WorkShift\CommandHandlers\CreateWorkShiftCommandHandler;
use Application\WorkShift\CommandHandlers\DeleteWorkShiftCommandHandler;
use Application\WorkShift\CommandHandlers\UpdateWorkShiftCommandHandler;
use Application\WorkShift\Commands\CreateWorkShiftCommand;
use Application\WorkShift\Commands\DeleteWorkShiftCommand;
use Application\WorkShift\Commands\UpdateWorkShiftCommand;
use Application\WorkShift\Contracts\WorkShiftServiceContract;
use Application\WorkShift\Queries\GetWorkShiftQuery;
use Application\WorkShift\Queries\GetWorkShiftsQuery;
use Application\WorkShift\QueriesHandlers\GetWorkShiftQueryHandler;
use Application\WorkShift\QueriesHandlers\GetWorkShiftsQueryHandler;
use Application\WorkShift\Services\WorkShiftService;
use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    public $singletons = [
        CommandBusContract::class => IlluminateCommandBus::class,
        QueryBusContract::class => IlluminateQueryBus::class,
    ];

    public $bindings = [
        UserServiceContract::class => UserService::class,
        CountryServiceContract::class => CountryService::class,
        CityServiceContract::class => CityService::class,
        PositionServiceContract::class => PositionService::class,
        WorkAreaServiceContract::class => WorkAreaService::class,
        WorkShiftServiceContract::class => WorkShiftService::class,
        SupervisorServiceContract::class => SupervisorService::class,
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
            UpdateUserCommand::class => UpdateUserCommandHandler::class,
            DeleteUserCommand::class => DeleteUserCommandHandler::class,
            CreateCountryCommand::class => CreateCountryCommandHandler::class,
            UpdateCountryCommand::class => UpdateCountryCommandHandler::class,
            DeleteCountryCommand::class => DeleteCountryCommandHandler::class,
            CreateCityCommand::class => CreateCityCommandHandler::class,
            UpdateCityCommand::class => UpdateCityCommandHandler::class,
            DeleteCityCommand::class => DeleteCityCommandHandler::class,
            CreatePositionCommand::class => CreatePositionCommandHandler::class,
            UpdatePositionCommand::class => UpdatePositionCommandHandler::class,
            DeletePositionCommand::class => DeletePositionCommandHandler::class,
            CreateWorkAreaCommand::class => CreateWorkAreaCommandHandler::class,
            UpdateWorkAreaCommand::class => UpdateWorkAreaCommandHandler::class,
            DeleteWorkAreaCommand::class => DeleteWorkAreaCommandHandler::class,
            CreateWorkShiftCommand::class => CreateWorkShiftCommandHandler::class,
            UpdateWorkShiftCommand::class => UpdateWorkShiftCommandHandler::class,
            DeleteWorkShiftCommand::class => DeleteWorkShiftCommandHandler::class,
            CreateSupervisorCommand::class => CreateSupervisorCommandHandler::class,
            UpdateSupervisorCommand::class => UpdateSupervisorCommandHandler::class,
            DeleteSupervisorCommand::class => DeleteSupervisorCommandHandler::class,
        ]);
    }

    protected function registerQueryHandlers(): void
    {
        $this->app->make(QueryBusContract::class)->register([
            GetHealthQuery::class => GetHealthQueryHandler::class,
            GetUsersQuery::class => GetUsersQueryHandler::class,
            GetUserQuery::class => GetUserQueryHandler::class,
            GetCountriesQuery::class => GetCountriesQueryHandler::class,
            GetCountryQuery::class => GetCountryQueryHandler::class,
            GetCitiesQuery::class => GetCitiesQueryHandler::class,
            GetCityQuery::class => GetCityQueryHandler::class,
            GetPositionsQuery::class => GetPositionsQueryHandler::class,
            GetPositionQuery::class => GetPositionQueryHandler::class,
            GetWorkAreasQuery::class => GetWorkAreasQueryHandler::class,
            GetWorkAreaQuery::class => GetWorkAreaQueryHandler::class,
            GetWorkShiftsQuery::class => GetWorkShiftsQueryHandler::class,
            GetWorkShiftQuery::class => GetWorkShiftQueryHandler::class,
            GetSupervisorsQuery::class => GetSupervisorsQueryHandler::class,
            GetSupervisorQuery::class => GetSupervisorQueryHandler::class,
        ]);
    }
}
