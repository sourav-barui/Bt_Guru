@extends('layouts.student_mobile')

@section('title', 'Pay Fee')

@section('mobile-content')
<!-- Payment Card -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="px-4 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white">
        <h1 class="text-xl font-bold">Pay Monthly Fee</h1>
        <p class="text-green-100 text-sm mt-1">{{ $fee->enrollment->course->title }}</p>
    </div>

    <!-- Fee Details -->
    <div class="p-4 border-b border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <p class="text-gray-500 text-sm">Month</p>
                <p class="font-semibold text-gray-900">{{ $fee->month_name }} {{ $fee->year }}</p>
            </div>
            <div class="text-right">
                <p class="text-gray-500 text-sm">Status</p>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $fee->status === 'overdue' ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }}">
                    {{ ucfirst($fee->status) }}
                </span>
            </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <p class="text-gray-600">Amount to Pay</p>
            <p class="text-3xl font-bold text-gray-900">₹{{ number_format($amount) }}</p>
        </div>
    </div>

    <!-- Payment Method -->
    <div class="p-4">
        <h3 class="font-semibold text-gray-900 mb-3">Select Payment Method</h3>

        <form method="POST" action="{{ route('student.fees.process-month', $fee) }}" enctype="multipart/form-data">
            @csrf

            <!-- UPI Option -->
            @if($upiId)
                <div class="mb-4">
                    <label class="flex items-start gap-3 p-4 border-2 border-green-200 rounded-xl bg-green-50 cursor-pointer">
                        <input type="radio" name="payment_method" value="upi" class="mt-1" checked>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Pay via UPI</p>
                            <p class="text-sm text-gray-600 mt-1">Scan QR or use UPI ID: {{ $upiId }}</p>
                            
                            <!-- QR Code -->
                            <div class="mt-3 flex justify-center">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($upiLink) }}" 
                                     alt="UPI QR Code" class="w-40 h-40 rounded-lg">
                            </div>
                            
                            <a href="{{ $upiLink }}" class="block mt-3 text-center py-2 bg-green-600 text-white rounded-lg font-medium">
                                Open UPI App
                            </a>
                        </div>
                    </label>
                </div>
            @endif

            <!-- Cash Option -->
            <div class="mb-4">
                <label class="flex items-center gap-3 p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50">
                    <input type="radio" name="payment_method" value="cash">
                    <div>
                        <p class="font-semibold text-gray-900">Pay Cash at Centre</p>
                        <p class="text-sm text-gray-500">Visit coaching centre and pay in person</p>
                    </div>
                </label>
            </div>

            <!-- Screenshot Upload -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Payment Screenshot (Optional)
                </label>
                <input type="file" name="screenshot" accept="image/*" 
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                <p class="text-xs text-gray-400 mt-1">Upload screenshot after UPI payment</p>
            </div>

            <!-- Transaction ID -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    UTR/Transaction ID
                </label>
                <input type="text" name="transaction_id" placeholder="Enter transaction reference" 
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg">
                Confirm Payment
            </button>
        </form>
    </div>
</div>

<!-- Instructions -->
<div class="mt-4 bg-blue-50 rounded-xl p-4 border border-blue-100">
    <h4 class="font-semibold text-blue-900 mb-2">How to Pay</h4>
    <ol class="text-sm text-blue-800 space-y-2 list-decimal list-inside">
        <li>Scan the QR code or tap "Open UPI App"</li>
        <li>Complete payment in your UPI app</li>
        <li>Save the transaction screenshot</li>
        <li>Enter UTR/Transaction ID</li>
        <li>Upload screenshot and confirm</li>
    </ol>
</div>
@endsection
