<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share current tenant with all views if available
        View::composer('*', function ($view) {
            if (app()->has('current_tenant')) {
                $view->with('currentTenant', app('current_tenant'));
            }
        });
    }
}
