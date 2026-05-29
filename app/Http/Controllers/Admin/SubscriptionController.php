<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['tenant', 'plan'])
            ->latest()
            ->get();
        
        $stats = [
            'total' => $subscriptions->count(),
            'active' => $subscriptions->where('status', 'active')->count(),
            'trial' => $subscriptions->where('status', 'trial')->count(),
            'expired' => $subscriptions->where('status', 'expired')->count(),
            'cancelled' => $subscriptions->where('status', 'cancelled')->count(),
            'pending_payment' => $subscriptions->where('payment_status', 'pending')->count(),
        ];

        return view('admin.subscriptions.index', compact('subscriptions', 'stats'));
    }

    public function show(Subscription $subscription)
    {
        $subscription->load(['tenant', 'plan']);
        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function edit(Subscription $subscription)
    {
        $subscription->load(['tenant', 'plan']);
        $plans = SubscriptionPlan::active()->ordered()->get();
        return view('admin.subscriptions.edit', compact('subscription', 'plans'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'plan_id' => 'nullable|exists:subscription_plans,id',
            'status' => 'required|in:trial,active,expired,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
            'notes' => 'nullable|string',
        ]);

        $subscription->update([
            'plan_id' => $request->plan_id ?? $subscription->plan_id,
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription updated successfully.');
    }

    public function cancel(Subscription $subscription)
    {
        $subscription->cancel();
        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription cancelled successfully.');
    }

    public function activate(Subscription $subscription)
    {
        $subscription->activate();
        return redirect()->route('admin.subscriptions.show', $subscription)
            ->with('success', 'Subscription activated successfully.');
    }
}
