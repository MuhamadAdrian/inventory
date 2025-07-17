<?php

namespace App\Providers;

use App\Models\ProductStock;
use App\Observers\ProductStockObserver;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

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
        ProductStock::observe(ProductStockObserver::class);

        Carbon::setLocale('id');
    }
}
