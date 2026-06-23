<?php

namespace App\Providers;

use App\Services\Payments\PesaPalService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PesaPalService::class, fn () => new PesaPalService(config('services.pesapal')));
    }

    public function boot(): void
    {
        //
    }
}
