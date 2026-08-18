<?php

namespace App\Providers;

use App\Models\Campaign;
use Illuminate\Support\Facades\View;
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
        // Share active campaigns with the storefront layout globally
        View::composer('layouts.storefront', function ($view) {
            if (! $view->offsetExists('campaigns')) {
                $view->with('campaigns', Campaign::currentlyActive()->get());
            }
        });
    }
}
