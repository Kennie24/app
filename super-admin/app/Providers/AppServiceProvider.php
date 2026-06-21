<?php

namespace App\Providers;

use App\Services\Payments\AirtelMoneyService;
use App\Services\Payments\MtnMomoService;
use App\Services\Payments\StripePaymentService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(StripePaymentService::class, fn () => new StripePaymentService(config('services.stripe')));
        $this->app->singleton(MtnMomoService::class,       fn () => new MtnMomoService(config('services.mtn_momo')));
        $this->app->singleton(AirtelMoneyService::class,   fn () => new AirtelMoneyService(config('services.airtel_money')));
    }

    public function boot(): void
    {
        //
    }
}
