<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Application\City\Contracts\CityRepositoryContract;
use Application\Country\Contracts\CountryRepositoryContract;
use Application\Position\Contracts\PositionRepositoryContract;
use Application\User\Contracts\UserRepositoryContract;
use Application\WorkArea\Contracts\WorkAreaRepositoryContract;
use Illuminate\Support\ServiceProvider;
use Infrastructure\City\Persistence\Repositories\CityRepository;
use Infrastructure\Country\Persistence\Repositories\CountryRepository;
use Infrastructure\Position\Persistence\Repositories\PositionRepository;
use Infrastructure\User\Persistence\Repositories\UserRepository;
use Infrastructure\WorkArea\Persistence\Repositories\WorkAreaRepository;

class InfrastructureServiceProvider extends ServiceProvider
{
    /**
     * Contract => implementation. Application code only knows the contract.
     *
     * @var array<class-string, class-string>
     */
    public $bindings = [
        UserRepositoryContract::class => UserRepository::class,
        CountryRepositoryContract::class => CountryRepository::class,
        CityRepositoryContract::class => CityRepository::class,
        PositionRepositoryContract::class => PositionRepository::class,
        WorkAreaRepositoryContract::class => WorkAreaRepository::class,
    ];
}
