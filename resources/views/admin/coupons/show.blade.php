@extends('layouts.admin')

@section('title', 'Coupon Details')
@section('page-title', 'Coupon: {{ $coupon->code_upper }}')

@section('page-content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Coupon Details</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn-primary">
                    Edit Coupon
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Code</p>
                    <p class="font-mono font-semibold text-gray-900 text-lg">{{ $coupon->code_upper }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Discount</p>
                    @if($coupon->discount_type === 'percentage')
                        <p class="font-semibold text-gray-900">{{ $coupon->discount_value }}% off</p>
                    @else
                        <p class="font-semibold text-gray-900">{{ number_format($coupon->discount_value, 2) }} off</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-500">Usage</p>
                    <p class="font-medium text-gray-900">{{ $coupon->used_count }} / {{ $coupon->max_uses ?? 'Unlimited' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="badge {{ $coupon->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Valid From</p>
                    <p class="font-medium text-gray-900">{{ $coupon->valid_from ? $coupon->valid_from->format('M d, Y') : 'No limit' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Valid Until</p>
                    <p class="font-medium text-gray-900">{{ $coupon->valid_until ? $coupon->valid_until->format('M d, Y') : 'No limit' }}</p>
                </div>
            </div>

            @if($coupon->description)
                <div class="mt-6">
                    <p class="text-sm text-gray-500">Description</p>
                    <p class="text-gray-900 mt-1">{{ $coupon->description }}</p>
                </div>
            @endif

            @if($coupon->applicable_plan_ids)
                <div class="mt-6">
                    <p class="text-sm text-gray-500">Applicable Plans</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($coupon->plans as $plan)
                            <span class="bg-blue-100 text-blue-700 text-sm px-3 py-1 rounded-full">{{ $plan->name }}</span>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="mt-6">
                    <p class="text-sm text-gray-500">Applicable Plans</p>
                    <p class="text-gray-900 mt-1">All plans</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
