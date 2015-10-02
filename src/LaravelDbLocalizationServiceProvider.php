<?php

namespace Despark\LaravelDbLocalization;

use Illuminate\Support\ServiceProvider;

class LaravelDbLocalizationServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind('laravel-db-localization', function ($app) {
            return new LaravelDbLocalization();
        });
    }
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/src/config/laravel-db-localization.php' => config_path('laravel-db-localization.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/src/migrations/' => base_path('/database/migrations'),
        ], 'migrations');
    }
}
