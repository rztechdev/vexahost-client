<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
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
        // Cegah Chrome warning: "was preloaded using link preload but not used" akibat Cloudflare Early Hints / Rocket Loader
        Vite::usePreloadTagAttributes(fn () => false);

        if (request()->header('x-forwarded-proto') === 'https' || (request()->server('HTTP_X_FORWARDED_PROTO') === 'https') || str_contains(request()->getHost(), 'ngrok')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
    }
}
