<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class StudentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('student.login');
        }

        $user = Auth::user();

        // Must be a student
        if (!$user->isStudent()) {
            abort(403, 'Unauthorized. Student access only.');
        }

        // Verify user belongs to current tenant
        $currentTenant = app('current_tenant');
        if ($currentTenant && $user->tenant_id !== $currentTenant->id) {
            abort(403, 'Unauthorized. User does not belong to this tenant.');
        }

        return $next($request);
    }
}
