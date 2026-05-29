<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\SubscriptionPlan;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withCount('plans')->latest()->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        return view('admin.coupons.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'applicable_plan_ids' => 'nullable|array',
            'applicable_plan_ids.*' => 'exists:subscription_plans,id',
            'is_active' => 'nullable|boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $coupon = Coupon::create([
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_uses' => $request->max_uses,
            'used_count' => 0,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'applicable_plan_ids' => $request->applicable_plan_ids,
            'is_active' => $request->has('is_active'),
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function show(Coupon $coupon)
    {
        $coupon->load('plans');
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        $plans = SubscriptionPlan::active()->ordered()->get();
        return view('admin.coupons.edit', compact('coupon', 'plans'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_uses' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'applicable_plan_ids' => 'nullable|array',
            'applicable_plan_ids.*' => 'exists:subscription_plans,id',
            'is_active' => 'nullable|boolean',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $coupon->update([
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'max_uses' => $request->max_uses,
            'valid_from' => $request->valid_from,
            'valid_until' => $request->valid_until,
            'applicable_plan_ids' => $request->applicable_plan_ids,
            'is_active' => $request->has('is_active'),
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }
}
