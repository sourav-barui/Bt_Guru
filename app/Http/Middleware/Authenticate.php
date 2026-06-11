<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Check if tenant subdomain
        $host = $request->getHost();
        $baseDomain = config('app.base_domain', 'btguru.test');
        $centralDomain = config('app.central_domain', 'btguru.test');
        
        if (str_ends_with($host, '.' . $baseDomain)) {
            return route('tenant.login');
        }

        // If main domain or localhost, redirect to tenant login
        if ($host === $centralDomain || $host === 'localhost' || $host === '127.0.0.1') {
            return route('tenant.login');
        }

        return route('admin.login');
    }
}
