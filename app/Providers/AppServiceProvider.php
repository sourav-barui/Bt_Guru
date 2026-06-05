<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

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

            // Trust Cloudflare proxies for proper HTTPS detection
            \Illuminate\Http\Request::setTrustedProxies(
                ['173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22',
                 '141.101.64.0/18', '108.162.192.0/18', '190.93.240.0/20', '188.114.96.0/20',
                 '197.234.240.0/22', '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13',
                 '104.24.0.0/14', '172.64.0.0/13', '131.0.72.0/22'],
                SymfonyRequest::HEADER_X_FORWARDED_PROTO |
                SymfonyRequest::HEADER_X_FORWARDED_HOST |
                SymfonyRequest::HEADER_X_FORWARDED_PORT
            );
        }

        // Share current tenant with all views if available
        View::composer('*', function ($view) {
            if (app()->has('current_tenant')) {
                $view->with('currentTenant', app('current_tenant'));
            }
        });
    }
}
