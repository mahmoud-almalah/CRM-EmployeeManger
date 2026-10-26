<?php

use App\Providers\AppServiceProvider;
use Application\Providers\ApplicationServiceProvider;
use Illuminate\Translation\TranslationServiceProvider;
use Infrastructure\Providers\InfrastructureServiceProvider;
use Spatie\Permission\PermissionServiceProvider;

return [
    AppServiceProvider::class,
    PermissionServiceProvider::class,
    TranslationServiceProvider::class,
    Spatie\TranslationLoader\TranslationServiceProvider::class,

    ApplicationServiceProvider::class,
    InfrastructureServiceProvider::class,
];
