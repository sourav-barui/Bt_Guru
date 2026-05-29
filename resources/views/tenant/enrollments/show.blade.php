@extends('layouts.tenant')

@section('title', 'Enrollment Details')
@section('page-title', 'Admission Details')

@section('page-content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Admission #{{ $enrollment->id }}</h2>
                <p class="text-sm text-gray-500 mt-1">Applied on {{ $enrollment->created_at->format('M d, Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="badge {{ $enrollment->status_badge_class }}">
                    {{ ucfirst($enrollment->enrollment_status) }}
                </span>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <!-- Student Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Student Information</h3>
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-sm">{{ substr($enrollment->student->name, 0, 2) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $enrollment->student->name }}</p>
                            <p class="text-sm text-gray-500">{{ $enrollment->student->email }}</p>
                        </div>
                    </div>
                    @if($enrollment->student->phone)
                        <p class="text-sm text-gray-600 mt-2">
                            <span class="font-medium">Phone:</span> {{ $enrollment->student->phone }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Course Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-semibold text-gray-900 mb-3">Course Information</h3>
                <div class="space-y-2">
                    <p class="font-medium text-gray-900">{{ $enrollment->course->title }}</p>
                    <p class="text-sm text-gray-500">{{ Str::limit($enrollment->course->description, 100) }}</p>
                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                        <span>Duration: {{ $enrollment->course->duration ?? '-' }}</span>
                        <span>Fees: ₹{{ number_format($enrollment->fees_total) }}</span>
                    </div>
                    @if($enrollment->course->teachers->count() > 0)
                        <p class="text-sm text-gray-600 mt-2">
                            <span class="font-medium">Teacher:</span> {{ $enrollment->course->teachers->first()->name }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Fee Status -->
        <div class="mt-6 bg-gray-50 rounded-lg p-4">
            <h3 class="font-semibold text-gray-900 mb-4">Fee Status</h3>
            <div class="grid md:grid-cols-3 gap-4 mb-4">
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900">₹{{ number_format($enrollment->fees_total) }}</p>
                    <p class="text-sm text-gray-500">Total Fees</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold {{ $enrollment->isPaymentCompleted() ? 'text-green-600' : 'text-orange-600' }}">
                        ₹{{ number_format($enrollment->fees_paid) }}
                    </p>
                    <p class="text-sm text-gray-500">Paid</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold {{ $enrollment->fees_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                        ₹{{ number_format($enrollment->fees_balance) }}
                    </p>
                    <p class="text-sm text-gray-500">Balance</p>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2.5">
                <div class="h-2.5 rounded-full {{ $enrollment->isPaymentCompleted() ? 'bg-green-500' : 'bg-orange-500' }}" 
                     style="width: {{ $enrollment->payment_percentage }}%"></div>
            </div>
            <p class="text-sm text-gray-500 mt-2 text-center">{{ number_format($enrollment->payment_percentage, 0) }}% Paid</p>
            <div class="mt-3 text-center">
                <span class="badge {{ $enrollment->payment_status_badge_class }}">
                    {{ ucfirst($enrollment->payment_status) }}
                </span>
            </div>
        </div>

        <!-- Timeline -->
        <div class="mt-6 bg-gray-50 rounded-lg p-4">
            <h3 class="font-semibold text-gray-900 mb-4">Timeline</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">Application Submitted</p>
                        <p class="text-sm text-gray-500">{{ $enrollment->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
                
                @if($enrollment->approved_at)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Application Approved</p>
                            <p class="text-sm text-gray-500">{{ $enrollment->approved_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @endif
                
                @if($enrollment->enrolled_at)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Enrollment Confirmed</p>
                            <p class="text-sm text-gray-500">{{ $enrollment->enrolled_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex items-center justify-between">
            <a href="{{ route('tenant.enrollments.index') }}" class="btn-secondary">
                Back to Admissions
            </a>
            
            @if($enrollment->enrollment_status === 'pending')
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('tenant.enrollments.approve', $enrollment) }}" class="inline">
                        @csrf
                        <button type="submit" class="btn-success">
                            Approve Admission
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
