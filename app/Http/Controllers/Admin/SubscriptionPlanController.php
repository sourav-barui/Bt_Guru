<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;

class SubscriptionPlanController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->ordered()->get();
        return view('admin.subscription_plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscription_plans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'duration_days' => 'required|integer|min:1',
            'trial_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_popular' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $plan = SubscriptionPlan::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'currency' => $request->currency,
            'duration_days' => $request->duration_days,
            'trial_days' => $request->trial_days ?? 0,
            'features' => $request->features,
            'is_popular' => $request->has('is_popular'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.subscription_plans.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    public function show(SubscriptionPlan $plan)
    {
        $plan->load('subscriptions.tenant');
        return view('admin.subscription_plans.show', compact('plan'));
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.subscription_plans.edit', compact('plan'));
    }

    public function update(Request $request, SubscriptionPlan $plan)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'duration_days' => 'required|integer|min:1',
            'trial_days' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_popular' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $plan->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'currency' => $request->currency,
            'duration_days' => $request->duration_days,
            'trial_days' => $request->trial_days ?? 0,
            'features' => $request->features,
            'is_popular' => $request->has('is_popular'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.subscription_plans.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'Cannot delete plan with active subscriptions.');
        }

        $plan->delete();
        return redirect()->route('admin.subscription_plans.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }
}
