@extends('layouts.admin')

@section('title', 'Payment Details')
@section('page-title', 'Payment Details')

@section('page-content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Payment Information</h3>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Tenant</p>
                    <p class="font-medium text-gray-900">{{ $payment->subscription->tenant->coaching_name }}</p>
                    <p class="text-sm text-gray-500">{{ $payment->subscription->tenant->subdomain }}.btguru.tech</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Plan</p>
                    <p class="font-medium text-gray-900">{{ $payment->subscription->plan->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Payment Method</p>
                    <p class="font-medium text-gray-900">{{ $payment->payment_method_label }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Amount</p>
                    <p class="font-semibold text-gray-900">{{ $payment->formatted_amount }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status</p>
                    <span class="badge {{ $payment->payment_status === 'completed' ? 'badge-success' : ($payment->payment_status === 'processing' ? 'badge-info' : ($payment->payment_status === 'failed' ? 'badge-danger' : 'badge-warning')) }}">
                        {{ ucfirst($payment->payment_status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Transaction ID</p>
                    <p class="font-medium text-gray-900">{{ $payment->transaction_id ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Created At</p>
                    <p class="font-medium text-gray-900">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                </div>
                @if($payment->paid_at)
                    <div>
                        <p class="text-sm text-gray-500">Paid At</p>
                        <p class="font-medium text-gray-900">{{ $payment->paid_at->format('M d, Y H:i') }}</p>
                    </div>
                @endif
            </div>

            @if($payment->razorpay_order_id)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Razorpay Order ID</p>
                    <p class="font-mono text-sm text-gray-900">{{ $payment->razorpay_order_id }}</p>
                </div>
            @endif

            @if($payment->upi_transaction_id)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500">UPI Transaction ID</p>
                    <p class="font-mono text-sm text-gray-900">{{ $payment->upi_transaction_id }}</p>
                </div>
            @endif

            @if($payment->notes)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Notes</p>
                    <p class="text-gray-900 mt-1">{{ $payment->notes }}</p>
                </div>
            @endif

            @if($payment->screenshot_path)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500">Payment Screenshot</p>
                    <div class="mt-2">
                        <a href="{{ asset('storage/' . $payment->screenshot_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            View Screenshot
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($payment->payment_status === 'processing' || $payment->payment_status === 'pending')
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Verify Payment</h3>
            </div>

            <div class="p-6">
                <form action="{{ route('admin.payments.verify', $payment) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Action</label>
                            <select name="action" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="approve">Approve Payment</option>
                                <option value="reject">Reject Payment</option>
                            </select>
                        </div>

                        <div id="reason-field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason</label>
                            <textarea name="reason" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter reason for rejection..."></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="btn-primary">Submit</button>
                            <a href="{{ route('admin.payments.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
document.querySelector('select[name="action"]').addEventListener('change', function() {
    const reasonField = document.getElementById('reason-field');
    if (this.value === 'reject') {
        reasonField.classList.remove('hidden');
    } else {
        reasonField.classList.add('hidden');
    }
});
</script>
@endpush
@endsection
