<?php

namespace Ntpages\LaravelRedirector;

use Ntpages\LaravelRedirector\Commands\CheckHealthiness;
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    public function boot()
    {
        $packageDir = dirname(__DIR__);

        $this->publishes(["$packageDir/config/redirector.php" => config_path('redirector.php')], 'config');

        $this->loadMigrationsFrom("$packageDir/database/migrations");

        if ($this->app->runningInConsole()) {
            $this->commands([CheckHealthiness::class]);
        }
    }
}
