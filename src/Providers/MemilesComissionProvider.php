<?php

namespace Memiles\Comission\Providers;

use Illuminate\Support\ServiceProvider;

class MemilesComissionProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->publishes([
            __DIR__.'/../resources/config/memiles_comission.php' => config_path('memiles_comission.php'),
        ]);
    }
}
