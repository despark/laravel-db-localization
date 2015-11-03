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

        // Load the config file
        $this->mergeConfigFrom(__DIR__.'/config/laravel-db-localization.php', 'laravel-db-localization');
    }
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        // publish config
        $this->publishes([
            __DIR__.'/config/laravel-db-localization.php' => config_path('laravel-db-localization.php'),
        ], 'config');

        // publish migrations
        $this->publishes([
            __DIR__.'/migrations/' => base_path('/database/migrations'),
        ], 'migrations');
    }
}
