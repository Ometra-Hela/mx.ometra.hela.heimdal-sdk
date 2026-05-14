<?php

namespace Ometra\HeimdalSdk;

use Illuminate\Support\ServiceProvider;

class HeimdalSdkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/heimdal-sdk.php', 'heimdal-sdk');

        $this->app->singleton(HeimdalSdk::class, function () {
            return new HeimdalSdk((array) config('heimdal-sdk', []));
        });

        $this->app->alias(HeimdalSdk::class, 'heimdal-sdk');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/heimdal-sdk.php' => config_path('heimdal-sdk.php'),
        ], 'heimdal-sdk-config');
    }
}
