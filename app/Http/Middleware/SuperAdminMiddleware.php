<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('admin.login');
        }

        $user = Auth::user();

        // Must be a super admin (no tenant_id) and have super_admin role
        if (!$user->isSuperAdmin()) {
            abort(403, 'Unauthorized. Super Admin access only.');
        }

        return $next($request);
    }
}
