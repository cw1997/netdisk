<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\PassportService;

class PassportServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton('App\Services\PassportService', function ($app) {
            return new PassportService();
        });
    }
}
