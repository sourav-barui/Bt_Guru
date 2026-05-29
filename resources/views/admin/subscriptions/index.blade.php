@extends('layouts.admin')

@section('title', 'Subscriptions')
@section('page-title', 'Tenant Subscriptions')

@section('page-content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Active</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Trial</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['trial'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Expired</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['expired'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Cancelled</p>
            <p class="text-2xl font-bold text-gray-600">{{ $stats['cancelled'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_payment'] }}</p>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">All Subscriptions</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th>Tenant</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Period</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subscriptions as $subscription)
                        <tr>
                            <td>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <span class="text-blue-600 font-bold text-xs">{{ substr($subscription->tenant->coaching_name, 0, 2) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $subscription->tenant->coaching_name }}</p>
                                        <p class="text-sm text-gray-500">{{ $subscription->tenant->subdomain }}.btguru.tech</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="font-medium text-gray-900">{{ $subscription->plan->name }}</p>
                                <p class="text-sm text-gray-500">{{ $subscription->plan->formatted_price }}</p>
                            </td>
                            <td>
                                <span class="badge {{ $subscription->status === 'active' ? 'badge-success' : ($subscription->status === 'trial' ? 'badge-info' : ($subscription->status === 'expired' ? 'badge-danger' : 'badge-warning')) }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                                @if($subscription->coupon_code_used)
                                    <span class="text-xs text-purple-600 ml-1">({{ $subscription->coupon_code_used }})</span>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <p class="text-sm text-gray-900">{{ $subscription->start_date ? $subscription->start_date->format('M d, Y') : '-' }}</p>
                                    <p class="text-sm text-gray-500">to {{ $subscription->end_date ? $subscription->end_date->format('M d, Y') : '-' }}</p>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $subscription->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($subscription->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No subscriptions found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
