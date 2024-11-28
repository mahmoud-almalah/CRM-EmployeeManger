<?php

declare(strict_types=1);

namespace Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

class InfrastructureServiceProvider extends ServiceProvider
{
    public $singletons = [

    ];

    public function register(): void {}

    public function boot(): void
    {
        $this->registerSingletons();
    }

    protected function registerSingletons(): void
    {
        foreach ($this->singletons as $interface => $implementation) {
            $this->app->singleton($interface, $implementation);
        }
    }
}
