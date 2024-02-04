<?php

namespace App\Providers;

use App\Services\Caixa;
use App\Services\Operacoes;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Caixa::class, function () {
            return new Caixa();
        });

        $this->app->singleton(Operacoes::class, function () {
            return new Operacoes($this->app->make(Caixa::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
