@extends('layouts.student_mobile')

@section('title', 'My Fees')

@section('mobile-content')
<!-- Fee Summary -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-200">
        <p class="text-sm text-gray-500">Total Fees</p>
        <p class="text-2xl font-bold text-gray-900">₹{{ number_format($totalFees) }}</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-200">
        <p class="text-sm text-gray-500">Total Paid</p>
        <p class="text-2xl font-bold text-green-600">₹{{ number_format($totalPaid) }}</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-200">
        <p class="text-sm text-gray-500">Balance Due</p>
        <p class="text-2xl font-bold {{ $totalBalance > 0 ? 'text-red-600' : 'text-green-600' }}">₹{{ number_format($totalBalance) }}</p>
    </div>
</div>

<!-- Fee Details -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="font-semibold text-gray-900">Course-wise Fee Details</h3>
    </div>
    <div class="divide-y divide-gray-100">
        @forelse($enrollments as $enrollment)
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $enrollment->course->title }}</h4>
                        <p class="text-sm text-gray-500">{{ $enrollment->course->duration }}</p>
                        <div class="mt-2">
                            <span class="badge {{ $enrollment->payment_status === 'completed' ? 'badge-success' : ($enrollment->payment_status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                Payment: {{ ucfirst($enrollment->payment_status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex-1 max-w-md">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span>Fee Progress</span>
                            <span class="font-medium {{ $enrollment->isPaymentCompleted() ? 'text-green-600' : 'text-orange-600' }}">
                                {{ number_format($enrollment->payment_percentage, 0) }}%
                            </span>
                        </div>
                        <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full {{ $enrollment->isPaymentCompleted() ? 'bg-green-500' : 'bg-orange-500' }} rounded-full transition-all" 
                                 style="width: {{ $enrollment->payment_percentage }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-sm text-gray-600 mt-2">
                            <span>Paid: ₹{{ number_format($enrollment->fees_paid) }}</span>
                            <span>Balance: ₹{{ number_format($enrollment->fees_total - $enrollment->fees_paid) }}</span>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        @if(!$enrollment->isPaymentCompleted())
                            <button class="btn-warning py-2 px-4" onclick="alert('Please contact administration to make payment. Balance due: ₹{{ number_format($enrollment->fees_total - $enrollment->fees_paid) }}')">
                                Pay Now
                            </button>
                        @else
                            <span class="text-green-600 font-medium flex items-center gap-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Paid
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                <p>No fee records found</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
