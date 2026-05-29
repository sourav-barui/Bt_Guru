@extends('layouts.admin')

@section('title', 'Subscription Details')
@section('page-title', 'Subscription: {{ $subscription->tenant->coaching_name }}')

@section('page-content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Subscription Details</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="btn-primary">
                    Edit Subscription
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Tenant</p>
                    <p class="font-medium text-gray-900">{{ $subscription->tenant->coaching_name }}</p>
                    <p class="text-sm text-gray-500">{{ $subscription->tenant->subdomain }}.btguru.tech</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Plan</p>
                    <p class="font-medium text-gray-900">{{ $subscription->plan->name }}</p>
                    <p class="text-sm text-gray-500">{{ $subscription->plan->formatted_price }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="badge {{ $subscription->status === 'active' ? 'badge-success' : ($subscription->status === 'trial' ? 'badge-info' : 'badge-danger') }}">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Payment Status</p>
                    <span class="badge {{ $subscription->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                        {{ ucfirst($subscription->payment_status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Start Date</p>
                    <p class="font-medium text-gray-900">{{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">End Date</p>
                    <p class="font-medium text-gray-900">{{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : '-' }}</p>
                </div>
                @if($subscription->trial_end_date)
                    <div>
                        <p class="text-sm text-gray-500">Trial End Date</p>
                        <p class="font-medium text-gray-900">{{ $subscription->trial_end_date->format('M d, Y') }}</p>
                    </div>
                @endif
                @if($subscription->coupon_code_used)
                    <div>
                        <p class="text-sm text-gray-500">Coupon Used</p>
                        <p class="font-medium text-gray-900">{{ $subscription->coupon_code_used }}</p>
                    </div>
                @endif
            </div>

            @if($subscription->original_price || $subscription->discount_amount)
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

            @if($subscription->notes)
                <div class="mt-6">
                    <p class="text-sm text-gray-500">Notes</p>
                    <p class="text-gray-900 mt-1">{{ $subscription->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Quick Actions</h3>
        </div>

        <div class="p-6 flex items-center gap-4">
            @if($subscription->status !== 'active')
                <form action="{{ route('admin.subscriptions.activate', $subscription) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary">Activate Subscription</button>
                </form>
            @endif

            @if($subscription->status !== 'cancelled')
                <form action="{{ route('admin.subscriptions.cancel', $subscription) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this subscription?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50">Cancel Subscription</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
