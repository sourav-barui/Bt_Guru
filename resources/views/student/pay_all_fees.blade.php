@extends('layouts.student_mobile')

@section('title', 'Pay All Fees')

@section('mobile-content')
<!-- Payment Card -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="px-4 py-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white">
        <h1 class="text-xl font-bold">Pay All Pending Fees</h1>
        <p class="text-green-100 text-sm mt-1">{{ $pendingFees->count() }} months total</p>
    </div>

    <!-- Fee Summary -->
    <div class="p-4 border-b border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <p class="text-gray-600">Total Amount</p>
            <p class="text-3xl font-bold text-gray-900">₹{{ number_format($totalAmount) }}</p>
        </div>

        <!-- Fee List -->
        <div class="space-y-2 mt-4">
            @foreach($pendingFees as $fee)
                <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full {{ $fee->status === 'overdue' ? 'bg-red-500' : 'bg-orange-500' }}"></span>
                        <span class="text-sm text-gray-700">{{ $fee->month_name }} {{ $fee->year }}</span>
                    </div>
                    <span class="text-sm font-medium text-gray-900">₹{{ number_format($fee->amount) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Payment Method -->
    <div class="p-4">
        <h3 class="font-semibold text-gray-900 mb-3">Select Payment Method</h3>

        <form method="POST" action="{{ route('student.fees.process-all') }}" enctype="multipart/form-data">
            @csrf

            <!-- UPI Option -->
            @if($upiId)
                <div class="mb-4">
                    <label class="flex items-start gap-3 p-4 border-2 border-green-200 rounded-xl bg-green-50 cursor-pointer">
                        <input type="radio" name="payment_method" value="upi" class="mt-1" checked>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-900">Pay via UPI</p>
                            <p class="text-sm text-gray-600 mt-1">One payment for all months</p>
                            
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
                <input type="text" name="transaction_id" placeholder="Enter transaction reference" required
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent">
            </div>

            <!-- Submit -->
            <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl shadow-lg">
                Confirm Payment of ₹{{ number_format($totalAmount) }}
            </button>
        </form>
    </div>
</div>

<!-- Instructions -->
<div class="mt-4 bg-blue-50 rounded-xl p-4 border border-blue-100">
    <h4 class="font-semibold text-blue-900 mb-2">How to Pay</h4>
    <ol class="text-sm text-blue-800 space-y-2 list-decimal list-inside">
        <li>Scan the QR code or tap "Open UPI App"</li>
        <li>Pay ₹{{ number_format($totalAmount) }} for all {{ $pendingFees->count() }} months</li>
        <li>Save the transaction screenshot</li>
        <li>Enter UTR/Transaction ID</li>
        <li>Upload screenshot and confirm</li>
    </ol>
</div>
@endsection
