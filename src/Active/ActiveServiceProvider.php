<?php

namespace Rainwater\Active;

use Illuminate\Support\ServiceProvider;

class ActiveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishing();
        $this->mergeConfigFrom(__DIR__ . '/../config/active_users.php', 'active_users');
    }

    /**
     * Register the application services
     *
     * @return  void
     */
    public function register()
    {
        $this->app->singleton(Active::class, function () {
            return new Active;
        });

        $this->app->alias(Active::class, 'active-users');
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    private function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            // Lumen lacks a config_path() helper, so we use base_path()
            $this->publishes([
                __DIR__ . '/../config/active_users.php' => base_path('config/active_users.php'),
            ], 'config');
        }
    }
}
