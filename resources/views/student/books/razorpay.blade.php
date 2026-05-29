@extends('layouts.student')

@section('title', 'Complete Payment')
@section('page-title', 'Complete Payment')

@section('page-content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-6 text-white text-center">
            <h1 class="text-xl font-bold">Complete Your Purchase</h1>
            <p class="text-white/80 mt-1">{{ $book->title }}</p>
        </div>

        <div class="p-6">
            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Order Type</span>
                    <span class="font-medium">{{ $order->order_type_label }}</span>
                </div>
                <div class="border-t border-gray-200 pt-2 mt-2">
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>Total Amount</span>
                        <span class="text-purple-600">₹{{ number_format($amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Button -->
            <button id="razorpay-btn" class="btn-primary w-full py-3 text-lg">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Pay Now
            </button>

            <a href="{{ route('student.books.index') }}" class="btn-secondary w-full mt-3 block text-center">Cancel</a>

            <!-- Security Note -->
            <div class="mt-6 flex items-center justify-center text-sm text-gray-500">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
                Secure payment powered by Razorpay
            </div>
        </div>
    </div>
</div>

<form id="razorpay-form" method="POST" action="{{ route('student.books.verify') }}" class="hidden">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
</form>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('razorpay-btn').addEventListener('click', function(e) {
    e.preventDefault();

    var options = {
        "key": "{{ $keyId }}",
        "amount": {{ $amount * 100 }},
        "currency": "INR",
        "name": "{{ isset($currentTenant) ? $currentTenant->coaching_name : 'BT Guru' }}",
        "description": "Purchase: {{ $book->title }}",
        "order_id": "{{ $razorpayOrder['id'] }}",
        "handler": function (response) {
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('razorpay-form').submit();
        },
        "prefill": {
            "name": "{{ auth()->user()->name }}",
            "email": "{{ auth()->user()->email }}",
            "contact": "{{ auth()->user()->phone ?? '' }}"
        },
        "theme": {
            "color": "#7c3aed"
        }
    };

    var rzp = new Razorpay(options);
    rzp.open();
});
</script>
@endsection
