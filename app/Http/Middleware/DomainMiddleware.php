<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DomainMiddleware
{
    public function handle(Request $request, Closure $next, string $domainType): Response
    {
        $host = $request->getHost();
        $centralDomain = config('app.central_domain');
        $adminSubdomain = config('app.admin_subdomain');

        switch ($domainType) {
            case 'admin':
                // Only allow admin subdomain
                if (!str_starts_with($host, $adminSubdomain . '.') && $host !== $adminSubdomain . '.' . $centralDomain) {
                    abort(404);
                }
                break;

            case 'tenant':
                // Only allow tenant subdomains (not admin or central)
                if ($host === $centralDomain || str_starts_with($host, $adminSubdomain . '.')) {
                    abort(404);
                }
                break;

            case 'central':
                // Only allow central domain
                if ($host !== $centralDomain) {
                    abort(404);
                }
                break;

            default:
                abort(404);
        }

        return $next($request);
    }
}
