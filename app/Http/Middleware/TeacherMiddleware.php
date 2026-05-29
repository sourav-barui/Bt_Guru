<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TeacherMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('teacher.login');
        }

        $user = Auth::user();

        // Must be a teacher
        if (!$user->isTeacher() && !$user->isTenantAdmin()) {
            abort(403, 'Unauthorized. Teacher access only.');
        }

        // Verify user belongs to current tenant
        $currentTenant = app('current_tenant');
        if ($currentTenant && $user->tenant_id !== $currentTenant->id) {
            abort(403, 'Unauthorized. User does not belong to this tenant.');
        }

        return $next($request);
    }
}
