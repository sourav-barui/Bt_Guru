@extends('layouts.tenant')

@section('title', 'Manual Payment')
@section('page-title', 'Manual Payment')

@section('page-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Manual Payment</h3>
        </div>

        <div class="p-6">
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                <p class="text-purple-800 font-medium">Amount to Pay: {{ number_format($amount, 2) }} INR</p>
                <p class="text-purple-600 text-sm">Plan: {{ $subscription->plan->name }}</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h4 class="font-medium text-gray-900 mb-2">Bank Details</h4>
                <div class="space-y-1 text-sm">
                    <p><span class="text-gray-500">Bank Name:</span> <span class="text-gray-900">Your Bank Name</span></p>
                    <p><span class="text-gray-500">Account Number:</span> <span class="text-gray-900">XXXXXXXXXX</span></p>
                    <p><span class="text-gray-500">IFSC Code:</span> <span class="text-gray-900">XXXXXXXXXXX</span></p>
                    <p><span class="text-gray-500">Account Holder:</span> <span class="text-gray-900">Your Name</span></p>
                </div>
                <p class="text-xs text-gray-500 mt-2">* Update bank details in settings</p>
            </div>

            <form action="{{ route('tenant.subscriptions.payments.manual_submit', $subscription->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID</label>
                        <input type="text" name="transaction_id" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="Enter bank transaction reference number">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                        <input type="date" name="payment_date" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                        <input type="text" name="bank_name" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                               placeholder="e.g., HDFC Bank, SBI, ICICI">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Screenshot/Receipt</label>
                        <input type="file" name="screenshot" required accept="image/*,.pdf"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <p class="text-xs text-gray-500 mt-1">Upload payment receipt or screenshot (JPG, PNG, PDF)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="notes" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-500"
                                  placeholder="Add any additional notes..."></textarea>
                    </div>

                    <button type="submit" class="w-full btn-primary bg-purple-600 hover:bg-purple-700">
                        Submit Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
