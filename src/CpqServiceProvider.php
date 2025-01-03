<?php

namespace PcbPlus\PcbCpq;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

class CpqServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\ImportFactorsCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }

        $this->publishes([
            __DIR__ . '/../database/imports' => App::databasePath('imports')
        ], 'cpq');
    }
}
