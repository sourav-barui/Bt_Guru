@extends('layouts.admin')

@section('title', 'Edit Subscription')
@section('page-title', 'Edit Subscription: {{ $subscription->tenant->coaching_name }}')

@section('page-content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Edit Subscription</h3>
        </div>

        <form method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plan</label>
                    <select name="plan_id" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ $subscription->plan_id === $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} ({{ $plan->formatted_price }})
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="trial" {{ $subscription->status === 'trial' ? 'selected' : '' }}>Trial</option>
                            <option value="active" {{ $subscription->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="expired" {{ $subscription->status === 'expired' ? 'selected' : '' }}>Expired</option>
                            <option value="cancelled" {{ $subscription->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                        <select name="payment_status" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="pending" {{ $subscription->payment_status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $subscription->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ $subscription->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                        @error('payment_status')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Add any notes about this subscription...">{{ old('notes', $subscription->notes) }}</textarea>
                    @error('notes')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('admin.subscriptions.show', $subscription) }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Cancel</a>
                <button type="submit" class="btn-primary">Update Subscription</button>
            </div>
        </form>
    </div>
</div>
@endsection
