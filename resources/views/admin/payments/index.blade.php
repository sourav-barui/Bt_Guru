@extends('layouts.admin')

@section('title', 'Payments')
@section('page-title', 'All Payments')

@section('page-content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Pending</p>
            <p class="text-2xl font-bold text-orange-600">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Processing</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['processing'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Completed</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Failed</p>
            <p class="text-2xl font-bold text-red-600">{{ $stats['failed'] }}</p>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Payment Transactions</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th>Tenant</th>
                        <th>Plan</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($payments as $payment)
                        <tr>
                            <td>
                                <p class="font-medium text-gray-900">{{ $payment->subscription->tenant->coaching_name }}</p>
                                <p class="text-sm text-gray-500">{{ $payment->subscription->tenant->subdomain }}.btguru.tech</p>
                            </td>
                            <td>
                                <p class="font-medium text-gray-900">{{ $payment->subscription->plan->name }}</p>
                            </td>
                            <td>
                                <span class="text-sm text-gray-600">{{ $payment->payment_method_label }}</span>
                            </td>
                            <td>
                                <span class="font-semibold text-gray-900">{{ $payment->formatted_amount }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $payment->payment_status === 'completed' ? 'badge-success' : ($payment->payment_status === 'processing' ? 'badge-info' : ($payment->payment_status === 'failed' ? 'badge-danger' : 'badge-warning')) }}">
                                    {{ ucfirst($payment->payment_status) }}
                                </span>
                            </td>
                            <td>
                                <span class="text-sm text-gray-600">{{ $payment->created_at->format('M d, Y') }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No payments found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
