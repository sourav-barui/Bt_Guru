<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS URLs in production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Share current tenant with all views if available
        View::composer('*', function ($view) {
            if (app()->has('current_tenant')) {
                $view->with('currentTenant', app('current_tenant'));
            }
        });
    }
}
