<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Tenant;

class TenantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind current tenant to the container
        $this->app->singleton('current_tenant', function () {
            return session('current_tenant_id') 
                ? Tenant::find(session('current_tenant_id')) 
                : null;
        });
    }

    public function boot(): void
    {
        //
    }
}
