@extends('layouts.tenant')

@section('title', 'UPI QR Payment')
@section('page-title', 'Pay via UPI QR Code')

@section('page-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">UPI QR Payment</h3>
        </div>

        <div class="p-6">
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <p class="text-green-800 font-medium">Amount to Pay: {{ number_format($amount, 2) }} INR</p>
                <p class="text-green-600 text-sm">Plan: {{ $subscription->plan->name }}</p>
            </div>

            <div class="text-center mb-6">
                <img src="{{ $qrCodeUrl }}" alt="UPI QR Code" class="mx-auto border border-gray-200 rounded-lg">
                <p class="text-sm text-gray-500 mt-2">Scan this QR code using any UPI app</p>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-500 mb-1">UPI ID</p>
                <p class="font-mono font-medium text-gray-900">{{ $upiId }}</p>
                <p class="text-sm text-gray-500 mt-1">Merchant Name</p>
                <p class="font-medium text-gray-900">{{ $merchantName }}</p>
            </div>

            <form action="{{ route('tenant.subscriptions.payments.upi_submit', $subscription->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">UPI Transaction ID</label>
                        <input type="text" name="upi_transaction_id" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="Enter your UPI transaction ID">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Screenshot</label>
                        <input type="file" name="screenshot" required accept="image/*"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <p class="text-xs text-gray-500 mt-1">Upload screenshot of successful payment</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                        <textarea name="notes" rows="2"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"
                                  placeholder="Add any additional notes..."></textarea>
                    </div>

                    <button type="submit" class="w-full btn-primary bg-green-600 hover:bg-green-700">
                        Submit Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
