<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user || !$user->tenant) {
            return redirect()->route('login')->with('error', 'Authentication required.');
        }

        $tenant = $user->tenant;
        $subscription = $tenant->subscriptions()->latest()->first();

        // Allow access if no subscription exists (for onboarding)
        if (!$subscription) {
            return redirect()->route('tenant.subscriptions.platform_plans')
                ->with('warning', 'Please subscribe to a plan to continue.');
        }

        // Check if subscription is active or in trial
        if (!$subscription->isActive() && !$subscription->isTrial()) {
            return redirect()->route('tenant.subscriptions.current')
                ->with('error', 'Your subscription has expired. Please renew to continue.');
        }

        return $next($request);
    }
}
