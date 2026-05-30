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
        // Force HTTPS only for central domain and admin subdomain (domains with SSL certs)
        if (app()->environment('production')) {
            $host = request()->getHost();
            $centralDomain = config('app.central_domain');
            $adminDomain = config('app.admin_subdomain') . '.' . $centralDomain;

            if ($host === $centralDomain || $host === $adminDomain || $host === 'www.' . $centralDomain) {
                URL::forceScheme('https');
            }
        }

        // Share current tenant with all views if available
        View::composer('*', function ($view) {
            if (app()->has('current_tenant')) {
                $view->with('currentTenant', app('current_tenant'));
            }
        });
    }
}
