<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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

    public function boot()
    {
        // Force all URLs to use APP_URL
        URL::forceRootUrl(config('app.url'));

        // If your ngrok URL uses HTTPS (it does)
        URL::forceScheme('https');
    }
}

