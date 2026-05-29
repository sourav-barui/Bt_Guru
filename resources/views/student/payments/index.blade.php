@extends('layouts.student_mobile')
@section('title', 'My Payments')

@section('mobile-content')

<!-- Header with Gradient -->
<div class="tb-header-gradient" style="background: linear-gradient(135deg, #059669 0%, #047857 50%, #065f46 100%);">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-sm text-white/80">Track Your</p>
            <h1 class="text-2xl font-bold text-white">My Payments</h1>
        </div>
        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
@php
    $totalCount = $payments->count();
    $pendingCount = $payments->where('status', 'pending')->count();
    $approvedCount = $payments->where('status', 'approved')->count();
    $rejectedCount = $payments->where('status', 'rejected')->count();
@endphp
<div class="tb-stats-row -mt-6 mb-4">
    <div class="tb-stat-card shadow-lg {{ $totalCount > 0 ? 'ring-2 ring-emerald-400' : '' }}">
        <p class="tb-stat-number text-emerald-600">{{ $totalCount }}</p>
        <p class="tb-stat-label">Total</p>
    </div>
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number {{ $approvedCount > 0 ? 'text-green-600' : 'text-gray-500' }}">{{ $approvedCount }}</p>
        <p class="tb-stat-label">Approved</p>
    </div>
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number {{ $pendingCount > 0 ? 'text-orange-600' : 'text-gray-500' }}">{{ $pendingCount }}</p>
        <p class="tb-stat-label">Pending</p>
    </div>
</div>

<!-- Quick Action -->
<div class="px-4 mb-4">
    <a href="{{ route('student.payments.create') }}" class="tb-btn-primary w-full justify-center bg-gradient-to-r from-emerald-500 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Submit New Payment
    </a>
</div>

@if(session('success'))
<div class="mx-4 mb-4" style="background:#dcfce7;border:1px solid #86efac;border-radius:12px;padding:12px 16px;font-size:13px;color:#16a34a;font-weight:600;">
    {{ session('success') }}
</div>
@endif

<!-- Payments List -->
<div class="px-4 pb-6">
    <h2 class="tb-section-title" style="margin: 0 0 12px 0;">Payment History</h2>
    
    @forelse($payments as $payment)
        @php
            $statusColor = $payment->status === 'approved' ? 'from-green-500 to-green-700' :
                          ($payment->status === 'pending' ? 'from-orange-500 to-orange-700' : 'from-red-500 to-red-700');
            $statusBadgeColor = $payment->status === 'approved' ? 'bg-green-100 text-green-700' :
                                ($payment->status === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700');
            $statusIcon = $payment->status === 'approved' ? 
                '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                ($payment->status === 'pending' ?
                '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' :
                '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>');
        @endphp
        
        <div class="tb-course-card mb-4">
            {{-- Header --}}
            <div class="h-16 flex items-center justify-center relative bg-gradient-to-br {{ $statusColor }}">
                <svg class="w-10 h-10 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                <span class="absolute top-3 right-3 px-2 py-1 rounded-full text-xs font-bold {{ $statusBadgeColor }} flex items-center gap-1">
                    {!! $statusIcon !!}
                    {{ ucfirst($payment->status) }}
                </span>
            </div>
            
            {{-- Body --}}
            <div class="p-4">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">
                            @if($payment->book)
                                {{ $payment->book->title }}
                            @elseif($payment->course)
                                {{ $payment->course->title }}
                            @else
                                —
                            @endif
                        </h3>
                        <p class="text-sm text-gray-500">{{ $payment->payment_type_label }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-xl text-gray-900">₹{{ number_format($payment->amount) }}</p>
                        @if($payment->month_label)
                            <p class="text-xs text-gray-500">{{ $payment->month_label }}</p>
                        @endif
                    </div>
                </div>
                
                <!-- Payment Details -->
                <div class="bg-gray-50 rounded-xl p-3 mb-3">
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-gray-500">Date:</span>
                            <span class="font-medium text-gray-700">{{ $payment->created_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Ref:</span>
                            <span class="font-medium text-gray-700">{{ $payment->reference_number ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                
                @if($payment->admin_remark)
                <div class="mb-3 p-3 rounded-xl {{ $payment->status === 'approved' ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-red-50 text-red-700 border border-red-100' }}">
                    <p class="text-sm">
                        <span class="font-bold">Admin:</span> {{ $payment->admin_remark }}
                    </p>
                </div>
                @endif
                
                <!-- Actions -->
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-400">{{ $payment->created_at->diffForHumans() }}</span>
                    @if($payment->screenshot)
                        <a href="{{ Storage::url($payment->screenshot) }}" target="_blank" class="text-emerald-600 font-bold text-sm flex items-center gap-1 hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            View Receipt
                        </a>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="tb-card text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            <p class="text-gray-700 font-bold text-lg mb-2">No Payments Yet</p>
            <p class="text-gray-500 text-sm mb-4">Submit a payment request to get enrolled in a course.</p>
            <a href="{{ route('student.payments.create') }}" class="tb-btn-primary inline-flex bg-gradient-to-r from-emerald-500 to-emerald-700 hover:from-emerald-600 hover:to-emerald-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Submit Payment
            </a>
        </div>
    @endforelse
</div>

@endsection
