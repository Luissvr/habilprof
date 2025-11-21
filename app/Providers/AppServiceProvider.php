<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EnvVal;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Validación del archivo .env según reglas R5.2.x
        EnvVal::validate();
    }
}
