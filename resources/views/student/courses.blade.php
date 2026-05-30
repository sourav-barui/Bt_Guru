@extends('layouts.student_mobile')

@section('title', 'My Courses')

@section('mobile-content')
<!-- Header with Gradient -->
<div class="tb-header-gradient">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-sm text-white/80">Your Learning</p>
            <h1 class="text-2xl font-bold text-white">My Courses</h1>
        </div>
        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
@php
    $activeEnrollments = $enrollments->filter(fn($e) => $e->enrollment_status !== 'dropped');
    $activeCount = $activeEnrollments->where('enrollment_status', 'active')->count();
    $pendingCount = $activeEnrollments->where('enrollment_status', 'pending')->count();
    $paidCount = $activeEnrollments->filter(fn($e) => $e->isPaymentCompleted())->count();
    $totalEnrollments = $activeEnrollments->count();
@endphp
<div class="tb-stats-row -mt-6 mb-4">
    <div class="tb-stat-card shadow-lg {{ $totalEnrollments > 0 ? 'ring-2 ring-violet-400' : '' }}">
        <p class="tb-stat-number text-violet-600">{{ $totalEnrollments }}</p>
        <p class="tb-stat-label">Enrolled</p>
    </div>
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number text-green-600">{{ $activeCount }}</p>
        <p class="tb-stat-label">Active</p>
    </div>
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number {{ $pendingCount > 0 ? 'text-orange-600' : 'text-gray-500' }}">{{ $pendingCount }}</p>
        <p class="tb-stat-label">Pending</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="px-4 mb-4">
    <a href="{{ route('student.courses.all') }}" class="tb-btn-secondary w-full justify-center">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Enroll in New Course
    </a>
</div>

<!-- Enrolled Courses -->
<div class="px-4 pb-6">
    <h2 class="tb-section-title" style="margin: 0 0 12px 0;">Your Enrolled Courses</h2>
    
    @forelse($enrollments as $enrollment)
        @php
            $isPaid = $enrollment->isPaymentCompleted();
            $isDropped = $enrollment->enrollment_status === 'dropped';
            $statusColor = $isDropped ? 'bg-red-100 text-red-700' :
                          ($enrollment->enrollment_status === 'active' ? 'bg-green-100 text-green-700' :
                          ($enrollment->enrollment_status === 'pending' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700'));
            $statusLabel = $isDropped ? 'Not Enrolled' : ucfirst($enrollment->enrollment_status);
            $headerGradient = $isDropped ? 'from-gray-400 to-gray-600' :
                              ($isPaid ? 'from-green-500 to-green-700' : 'from-orange-500 to-orange-700');
        @endphp

        <div class="tb-course-card mb-4 {{ $isDropped ? 'opacity-75' : '' }}">
            {{-- Header --}}
            <div class="h-20 flex items-center justify-center relative bg-gradient-to-br {{ $headerGradient }}">
                <svg class="w-12 h-12 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="absolute top-3 right-3 px-2 py-1 rounded-full text-xs font-bold {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            </div>

            {{-- Body --}}
            <div class="p-4">
                <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $enrollment->course->title }}</h3>
                <p class="text-sm text-gray-500 mb-3">{{ $enrollment->course->duration }}</p>

                @if($enrollment->course->teachers->count() > 0)
                    <div class="flex items-center gap-2 mb-3 text-sm text-gray-600">
                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                            {{ strtoupper(substr($enrollment->course->teachers->first()->name,0,1)) }}
                        </div>
                        <span>{{ $enrollment->course->teachers->first()->name }}</span>
                    </div>
                @endif

                @if($isDropped)
                    <div class="bg-red-50 rounded-xl p-3 mb-3 text-center">
                        <p class="text-sm text-red-600 font-medium">You are no longer enrolled in this course.</p>
                        <p class="text-xs text-red-500 mt-1">Contact admin for assistance.</p>
                    </div>
                @else
                    <!-- Payment Progress -->
                    <div class="bg-gray-50 rounded-xl p-3 mb-3">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-500">Fee Payment</span>
                            <span class="font-bold {{ $isPaid ? 'text-green-600' : 'text-orange-600' }}">
                                {{ number_format($enrollment->payment_percentage, 0) }}%
                            </span>
                        </div>
                        <div class="tb-progress-bg">
                            <div class="tb-progress-fill {{ $isPaid ? 'bg-green-500' : 'bg-orange-500' }}" style="width: {{ $enrollment->payment_percentage }}%"></div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500 mt-2">
                            <span>Paid: ₹{{ number_format($enrollment->fees_paid) }}</span>
                            <span>Total: ₹{{ number_format($enrollment->fees_total) }}</span>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    @if($isDropped)
                        <a href="{{ route('student.courses.all') }}" class="tb-btn-secondary flex-1 justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Re-Enroll
                        </a>
                    @else
                        <a href="{{ route('student.courses.access', $enrollment->course) }}" class="tb-btn-primary flex-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Access
                        </a>
                        @if(!$isPaid)
                            <a href="{{ route('student.fees') }}" class="tb-btn-secondary bg-orange-100 text-orange-700 hover:bg-orange-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                </svg>
                                Pay
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="tb-card text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p class="text-gray-700 font-bold text-lg mb-2">No Courses Enrolled</p>
            <p class="text-gray-500 text-sm mb-4">You haven't enrolled in any courses yet.</p>
            <a href="{{ route('student.courses.all') }}" class="tb-btn-primary inline-flex">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Browse Courses
            </a>
        </div>
    @endforelse
</div>
@endsection
