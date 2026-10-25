<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Application\User\Contracts\UserRepositoryContract;
use Illuminate\Support\ServiceProvider;
use Infrastructure\User\Persistence\Repositories\UserRepository;

class InfrastructureServiceProvider extends ServiceProvider
{
    /**
     * Contract => implementation. Application code only knows the contract.
     *
     * @var array<class-string, class-string>
     */
    public $bindings = [
        UserRepositoryContract::class => UserRepository::class,
    ];
}
