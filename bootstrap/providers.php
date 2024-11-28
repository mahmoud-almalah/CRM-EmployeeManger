<?php

return array(
    App\Providers\AppServiceProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
    Illuminate\Translation\TranslationServiceProvider::class,
    Spatie\TranslationLoader\TranslationServiceProvider::class,

    Application\Providers\ApplicationServiceProvider::class,
    Infrastructure\Providers\InfrastructureServiceProvider::class,
);
