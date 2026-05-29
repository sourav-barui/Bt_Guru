<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SingleSessionMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isStudent()) {
            // Force fresh DB query to get current session ID (bypass any caching)
            $freshSessionId = DB::table('users')
                ->where('id', $user->id)
                ->value('current_session_id');

            // If no session is tracked (e.g., after admin logout), allow this session
            if (is_null($freshSessionId)) {
                // Update the session tracking for this new login
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'current_session_id' => session()->getId(),
                        'last_login_ip' => $request->ip(),
                        'last_login_at' => now(),
                    ]);
                return $next($request);
            }

            // Check if the current session matches the stored session
            if ($freshSessionId !== session()->getId()) {
                // Session mismatch - logout this user
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('student.login')
                    ->with('error', 'Your session has been terminated. Another device has logged into your account.');
            }
        }

        return $next($request);
    }
}
