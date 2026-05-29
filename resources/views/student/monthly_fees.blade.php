@extends('layouts.student_mobile')

@section('title', 'Monthly Fees')

@section('mobile-content')
<!-- Fee Summary Cards -->
<div class="mb-6 space-y-3">
    <!-- Total Pending -->
    <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-2xl p-4 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm">Total Pending</p>
                <p class="text-3xl font-bold">₹{{ number_format($totalPending) }}</p>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        @if($totalPending > 0)
            <div class="mt-3 pt-3 border-t border-white/20">
                <p class="text-sm text-orange-100">{{ $overdueCount }} overdue fee(s)</p>
            </div>
        @endif
    </div>

    <!-- Quick Pay All -->
    @if($totalPending > 0)
        <a href="{{ route('student.fees.pay-all') }}" class="block bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-4 text-white shadow-lg text-center">
            <p class="font-semibold text-lg">Pay All Pending Fees</p>
            <p class="text-sm text-green-100 mt-1">Quick checkout for ₹{{ number_format($totalPending) }}</p>
        </a>
    @endif
</div>

<!-- Monthly Fees by Course -->
@foreach($enrollments as $enrollment)
    @php
        $pendingFees = $enrollment->pendingMonthlyFees;
    @endphp
    
    @if($pendingFees->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-4 overflow-hidden">
            <!-- Course Header -->
            <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">{{ $enrollment->course->title }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">{{ $pendingFees->count() }} pending month(s)</p>
            </div>

            <!-- Fee List -->
            <div class="divide-y divide-gray-100">
                @foreach($pendingFees as $fee)
                    <div class="p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center
                                {{ $fee->status === 'overdue' ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $fee->month_name }} {{ $fee->year }}</p>
                                <p class="text-xs {{ $fee->status === 'overdue' ? 'text-red-500' : 'text-orange-500' }}">
                                    {{ $fee->status === 'overdue' ? '⚠️ Overdue' : '⏳ Pending' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">₹{{ number_format($fee->amount) }}</p>
                            <a href="{{ route('student.fees.pay-month', $fee->id) }}" 
                               class="inline-block mt-1 px-4 py-1.5 bg-green-500 text-white text-sm font-medium rounded-full">
                                Pay Now
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pay All for This Course -->
            <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
                <a href="{{ route('student.fees.pay-course', $enrollment->id) }}" 
                   class="block w-full py-2.5 bg-green-500 text-white text-center font-medium rounded-xl">
                    Pay All for {{ $enrollment->course->title }} (₹{{ number_format($pendingFees->sum('amount')) }})
                </a>
            </div>
        </div>
    @endif
@endforeach

<!-- Paid Fees History -->
@if($paidFees->count() > 0)
    <div class="mt-6">
        <h3 class="font-semibold text-gray-900 mb-3 px-1">Payment History</h3>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @foreach($paidFees as $fee)
                <div class="p-4 flex items-center justify-between border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $fee->month_name }} {{ $fee->year }}</p>
                            <p class="text-xs text-gray-500">{{ $fee->enrollment->course->title }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-green-600">₹{{ number_format($fee->amount) }}</p>
                        <p class="text-xs text-gray-400">{{ $fee->paid_at?->format('d M Y') }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Empty State -->
@if($totalPending === 0 && $paidFees->count() === 0)
    <div class="text-center py-12">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h3 class="text-lg font-semibold text-gray-900">All Fees Paid!</h3>
        <p class="text-gray-500 mt-1">You have no pending fees.</p>
    </div>
@endif
@endsection
