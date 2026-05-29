<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $roles = explode('|', $role);

        foreach ($roles as $r) {
            if ($user->hasRole($r)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized access. Required role: ' . str_replace('|', ' or ', $role));
    }
}
