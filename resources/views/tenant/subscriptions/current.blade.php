@extends('layouts.tenant')

@section('title', 'Current Subscription')
@section('page-title', 'My Subscription')

@section('page-content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Subscription Details</h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Plan</p>
                    <p class="font-medium text-gray-900 text-lg">{{ $subscription->plan->name }}</p>
                    <p class="text-sm text-gray-500">{{ $subscription->plan->description }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="badge {{ $subscription->status === 'active' ? 'badge-success' : ($subscription->status === 'trial' ? 'badge-info' : 'badge-danger') }}">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Start Date</p>
                    <p class="font-medium text-gray-900">{{ $subscription->start_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">End Date</p>
                    <p class="font-medium text-gray-900">{{ $subscription->end_date->format('M d, Y') }}</p>
                </div>
                @if($subscription->trial_end_date)
                    <div>
                        <p class="text-sm text-gray-500">Trial End Date</p>
                        <p class="font-medium text-gray-900">{{ $subscription->trial_end_date->format('M d, Y') }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-sm text-gray-500">Days Remaining</p>
                    <p class="font-medium text-gray-900">{{ $subscription->days_remaining }} days</p>
                </div>
            </div>

            @if($subscription->coupon_code_used)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Coupon Applied</p>
                    <p class="font-medium text-purple-600">{{ $subscription->coupon_code_used }}</p>
                </div>
            @endif

            @if($subscription->original_price)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Original Price</p>
                            <p class="font-medium text-gray-900">{{ number_format($subscription->original_price, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Discount</p>
                            <p class="font-medium text-green-600">-{{ number_format($subscription->discount_amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Final Price</p>
                            <p class="font-semibold text-gray-900">{{ number_format($subscription->final_price, 2) }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($subscription->payment_status === 'pending')
                <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800 font-medium">Payment Pending</p>
                    <p class="text-yellow-600 text-sm mt-1">Please complete your payment to activate your subscription.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Plan Features</h3>
        </div>

        <div class="p-6">
            @if($subscription->plan->features)
                <ul class="space-y-3">
                    @foreach(is_array($subscription->plan->features) ? $subscription->plan->features : json_decode($subscription->plan->features, true) as $feature)
                        <li class="flex items-start text-gray-700">
                            <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ $feature }}
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500">No features listed for this plan.</p>
            @endif
        </div>
    </div>

    @if($subscription->status === 'active' || $subscription->status === 'trial')
        <div class="mt-6 text-center">
            <a href="{{ route('tenant.dashboard') }}" class="btn-primary">Go to Dashboard</a>
        </div>
    @endif
</div>
@endsection
