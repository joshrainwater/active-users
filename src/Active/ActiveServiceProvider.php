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
}
