<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AuthentificationServiceInterface;
use App\Services\AuthentificationPassport;
use App\Services\AuthentificationSanctum;

class AuthCustomServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(AuthentificationServiceInterface::class, function ($app) {
            return new AuthentificationPassport();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
