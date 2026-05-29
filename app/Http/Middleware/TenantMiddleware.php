<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;

class TenantMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();
        $centralDomain = config('app.central_domain');
        $adminSubdomain = config('app.admin_subdomain');

        // Skip for central domain and admin subdomain
        if ($host === $centralDomain || str_starts_with($host, $adminSubdomain . '.')) {
            return $next($request);
        }

        // Extract subdomain from host
        $subdomain = $this->extractSubdomain($host, $centralDomain);

        if (!$subdomain) {
            // Check for custom domain
            $tenant = Tenant::byCustomDomain($host)->active()->first();
            
            if (!$tenant) {
                abort(404, 'Tenant not found');
            }
        } else {
            // Find tenant by subdomain
            $tenant = Tenant::bySubdomain($subdomain)->active()->first();

            if (!$tenant) {
                abort(404, 'Tenant not found');
            }
        }

        // Check if tenant is active and not expired
        if (!$tenant->isActive()) {
            if ($tenant->expires_at && $tenant->expires_at->isPast()) {
                abort(403, 'Tenant subscription has expired');
            }
            abort(403, 'Tenant is not active');
        }

        // Store tenant in session and app container
        session(['current_tenant_id' => $tenant->id]);
        app()->instance('current_tenant', $tenant);

        // Share tenant with views
        view()->share('currentTenant', $tenant);

        return $next($request);
    }

    private function extractSubdomain(string $host, string $centralDomain): ?string
    {
        if (!str_ends_with($host, $centralDomain)) {
            return null;
        }

        $subdomain = str_replace('.' . $centralDomain, '', $host);
        
        // If subdomain is same as host, no subdomain exists
        if ($subdomain === $host) {
            return null;
        }

        return $subdomain;
    }
}
