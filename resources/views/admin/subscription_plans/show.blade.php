@extends('layouts.admin')

@section('title', 'Subscription Plan Details')
@section('page-title', 'Subscription Plan: {{ $plan->name }}')

@section('page-content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Plan Details</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.subscription_plans.edit', $plan) }}" class="btn-primary">
                    Edit Plan
                </a>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Name</p>
                    <p class="font-medium text-gray-900">{{ $plan->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Price</p>
                    <p class="font-medium text-gray-900">{{ $plan->formatted_price }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Duration</p>
                    <p class="font-medium text-gray-900">{{ $plan->duration_text }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Trial Period</p>
                    <p class="font-medium text-gray-900">{{ $plan->trial_days > 0 ? $plan->trial_days . ' days' : 'No trial' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="badge {{ $plan->is_active ? 'badge-success' : 'badge-danger' }}">
                        {{ $plan->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Active Subscriptions</p>
                    <p class="font-medium text-gray-900">{{ $plan->subscriptions_count }}</p>
                </div>
            </div>

            @if($plan->description)
                <div class="mt-6">
                    <p class="text-sm text-gray-500">Description</p>
                    <p class="text-gray-900 mt-1">{{ $plan->description }}</p>
                </div>
            @endif

            @if($plan->features)
                <div class="mt-6">
                    <p class="text-sm text-gray-500">Features</p>
                    <ul class="mt-2 space-y-1">
                        @foreach(is_array($plan->features) ? $plan->features : json_decode($plan->features, true) as $feature)
                            <li class="flex items-center text-gray-900">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Subscriptions</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th>Tenant</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Payment</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($plan->subscriptions as $subscription)
                        <tr>
                            <td>
                                <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $subscription->tenant->coaching_name }}
                                </a>
                            </td>
                            <td>
                                <span class="badge {{ $subscription->status === 'active' ? 'badge-success' : ($subscription->status === 'trial' ? 'badge-info' : 'badge-danger') }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td>{{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : '-' }}</td>
                            <td>{{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : '-' }}</td>
                            <td>
                                <span class="badge {{ $subscription->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($subscription->payment_status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                No subscriptions for this plan yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
