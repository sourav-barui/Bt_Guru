@extends('layouts.student_mobile')

@section('title', 'Dashboard')

@section('mobile-content')
<!-- Testbook Style Header -->
<div class="tb-header-gradient">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-sm text-white/80">Welcome back,</p>
            <h1 class="text-2xl font-bold text-white">{{ Auth::user()->name }}</h1>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="touch-btn p-2 bg-white/20 rounded-full">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

<!-- Quick Stats Row -->
<div class="tb-stats-row -mt-6 mb-4">
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number">{{ $stats['enrolled_courses'] }}</p>
        <p class="tb-stat-label">My Courses</p>
    </div>
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number {{ $stats['pending_payments'] > 0 ? 'text-red-500' : 'text-green-600' }}">{{ $stats['pending_payments'] }}</p>
        <p class="tb-stat-label">Pending</p>
    </div>
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number text-green-600">{{ $stats['completed_courses'] }}</p>
        <p class="tb-stat-label">Completed</p>
    </div>
</div>

<!-- My Courses Section - Compact List Style -->
<div class="px-4 mt-6">
    <div class="flex items-center justify-between mb-4">
        <h2 class="tb-section-title" style="margin: 0;">Continue Learning</h2>
        <a href="{{ route('student.courses') }}" class="text-sm text-indigo-600 font-semibold">See All</a>
    </div>

    @php
        $colors = [
            'from-blue-500 to-blue-600',
            'from-green-500 to-emerald-600',
            'from-purple-500 to-purple-600',
            'from-orange-500 to-orange-600',
            'from-pink-500 to-pink-600',
            'from-teal-500 to-teal-600',
            'from-indigo-500 to-indigo-600',
            'from-red-500 to-red-600',
        ];
    @endphp

    @forelse($activeEnrollments->take(3) as $index => $enrollment)
        @php
            $colorClass = $colors[$index % count($colors)];
            $showPercentage = $enrollment->payment_percentage < 100;
        @endphp
        <a href="{{ route('student.courses.access', $enrollment->course) }}" class="block bg-white rounded-xl border border-gray-100 shadow-sm mb-3 overflow-hidden hover:shadow-md transition-shadow">
            <div class="flex items-center p-3">
                <!-- Icon with different colors -->
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $colorClass }} flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>

                <!-- Content -->
                <div class="ml-5 flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $enrollment->course->title }}</h3>
                        @php
                            // Check for monthly fees due within 5 days
                            $upcomingFee = $enrollment->monthlyFees()
                                ->where('status', 'pending')
                                ->where(function($q) {
                                    $q->whereYear('year', now()->year)
                                      ->whereMonth('month', now()->month);
                                })
                                ->first();
                            $showPayButton = !$enrollment->isPaymentCompleted() || ($upcomingFee && now()->day >= 25);
                        @endphp
                        @if($showPayButton)
                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">Pay</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $enrollment->course->duration }}</p>

                    <!-- Progress Bar - only show if less than 100% -->
                    @if($showPercentage)
                        <div class="flex items-center gap-2 mt-2">
                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $enrollment->isPaymentCompleted() ? 'bg-green-500' : 'bg-orange-500' }} rounded-full" style="width: {{ $enrollment->payment_percentage }}%"></div>
                            </div>
                            <span class="text-xs font-medium {{ $enrollment->isPaymentCompleted() ? 'text-green-600' : 'text-orange-600' }}">
                                {{ number_format($enrollment->payment_percentage, 0) }}%
                            </span>
                        </div>
                    @endif
                </div>

                <!-- Arrow -->
                <div class="ml-2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </div>
        </a>
    @empty
        <div class="tb-card text-center py-8">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p class="text-gray-500 mb-4">No courses enrolled yet</p>
            <a href="{{ route('student.courses.all') }}" class="tb-btn-primary inline-flex">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Explore Courses
            </a>
        </div>
    @endforelse
</div>

<!-- Explore Categories Grid -->
<div class="px-4 mt-6">
    <h2 class="tb-section-title" style="margin: 0 0 12px 0;">Explore</h2>
    <div class="tb-grid">
        <a href="{{ route('student.courses') }}" class="tb-grid-item">
            <div class="tb-grid-icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">My Courses</span>
        </a>
        <a href="{{ route('student.courses.all') }}" class="tb-grid-item">
            <div class="tb-grid-icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">All Courses</span>
        </a>
        <a href="{{ route('student.fees') }}" class="tb-grid-item">
            <div class="tb-grid-icon">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Fees</span>
        </a>
        <a href="{{ route('student.exams.index') }}" class="tb-grid-item">
            <div class="tb-grid-icon" style="background: linear-gradient(135deg, #f3e8ff, #e9d5ff); color: #7c3aed;">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Exams</span>
        </a>
    </div>
</div>

<!-- Notices Section -->
@if($notices->count() > 0)
<div class="px-4 mt-6 pb-4">
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
        </svg>
        <h2 class="text-lg font-bold text-gray-900">Notices</h2>
    </div>
    @foreach($notices->take(3) as $notice)
        <a href="{{ route('student.notices.show', $notice) }}" class="block tb-card mb-3" style="padding: 12px 16px;">
            <div class="flex items-start gap-3">
                <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0 {{ $notice->type === 'urgent' ? 'bg-red-500' : ($notice->type === 'important' ? 'bg-orange-500' : 'bg-blue-500') }}"></div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 text-sm line-clamp-1">{{ $notice->title }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $notice->created_at->diffForHumans() }}</p>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $notice->type === 'urgent' ? 'bg-red-100 text-red-700' : ($notice->type === 'important' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                        View
                    </span>
                    @if($notice->is_pinned)
                        <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.293 2.293a1 1 0 011.414 0l7 7A1 1 0 0117 11h-1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-3a1 1 0 00-1-1H9a1 1 0 00-1 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-6H3a1 1 0 01-.707-1.707l7-7z"/>
                        </svg>
                    @endif
                </div>
            </div>
        </a>
    @endforeach
</div>
@endif
@endsection
