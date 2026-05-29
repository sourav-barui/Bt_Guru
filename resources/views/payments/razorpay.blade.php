@extends('layouts.tenant')

@section('title', 'Razorpay Payment')
@section('page-title', 'Complete Payment')

@section('page-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-gray-900">Razorpay Payment</h3>
        </div>

        <div class="p-6">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-blue-800 font-medium">Amount to Pay: {{ number_format($amount, 2) }} INR</p>
                <p class="text-blue-600 text-sm">Plan: {{ $subscription->plan->name }}</p>
            </div>

            <form id="razorpay-form" action="{{ route('tenant.subscriptions.payments.razorpay_verify') }}" method="POST">
                @csrf
                <input type="hidden" name="razorpay_order_id" value="{{ $order['id'] }}">
                <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                <input type="hidden" name="razorpay_signature" id="razorpay_signature">

                <button type="button" id="rzp-button" class="w-full btn-primary py-4 text-lg">
                    Pay with Razorpay
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    'key': '{{ $keyId }}',
    'amount': '{{ $order['amount'] }}',
    'currency': 'INR',
    'name': 'BT Guru',
    'description': 'Subscription Payment',
    'order_id': '{{ $order['id'] }}',
    'handler': function (response) {
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_signature').value = response.razorpay_signature;
        document.getElementById('razorpay-form').submit();
    },
    'prefill': {
        'name': '{{ Auth::user()->name }}',
        'email': '{{ Auth::user()->email }}',
    },
    'theme': {
        'color': '#3B82F6'
    }
};

var rzp1 = new Razorpay(options);
document.getElementById('rzp-button').onclick = function(e) {
    rzp1.open();
    e.preventDefault();
}
</script>
@endpush
@endsection
