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

    @forelse($activeEnrollments->take(3) as $enrollment)
        <a href="{{ route('student.courses.access', $enrollment->course) }}" class="block bg-white rounded-xl border border-gray-100 shadow-sm mb-3 overflow-hidden hover:shadow-md transition-shadow">
            <div class="flex items-center p-3">
                <!-- Icon -->
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>

                <!-- Content -->
                <div class="ml-3 flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 text-sm truncate">{{ $enrollment->course->title }}</h3>
                        @if($enrollment->isPaymentCompleted())
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Paid</span>
                        @else
                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">Pay</span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $enrollment->course->duration }}</p>

                    <!-- Progress Bar -->
                    <div class="flex items-center gap-2 mt-2">
                        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full {{ $enrollment->isPaymentCompleted() ? 'bg-green-500' : 'bg-orange-500' }} rounded-full" style="width: {{ $enrollment->payment_percentage }}%"></div>
                        </div>
                        <span class="text-xs font-medium {{ $enrollment->isPaymentCompleted() ? 'text-green-600' : 'text-orange-600' }}">
                            {{ number_format($enrollment->payment_percentage, 0) }}%
                        </span>
                    </div>
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
        <div class="text-center py-8 text-gray-500">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p>No active courses</p>
            <a href="{{ route('student.courses') }}" class="text-indigo-600 text-sm mt-2 inline-block">Browse Courses</a>
        </div>
    @endforelse
</div>

<!-- Notices Section -->
@if($notices->count() > 0)
<div class="px-4 mt-6">
    <div class="flex items-center justify-between mb-3">
        <h2 class="tb-section-title" style="margin: 0;">Notices</h2>
        <span class="text-xs text-gray-500">{{ $notices->count() }} new</span>
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

<!-- Upcoming Schedule Section -->
@if($upcomingSchedule->count() > 0)
<div class="px-4 mt-6 mb-8">
    <div class="flex items-center justify-between mb-3">
        <h2 class="tb-section-title" style="margin: 0;">Today's Schedule</h2>
        <a href="{{ route('student.schedule') }}" class="text-sm text-indigo-600 font-semibold">Full Schedule</a>
    </div>

    <div class="space-y-3">
        @foreach($upcomingSchedule as $schedule)
            <div class="tb-card p-4 flex items-start gap-3">
                <div class="flex-shrink-0 w-14 text-center">
                    <p class="text-xs text-gray-500">{{ date('h:i A', strtotime($schedule->start_time)) }}</p>
                    <div class="w-10 h-10 mx-auto mt-1 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-900 text-sm">{{ $schedule->subject->name }}</h3>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $schedule->chapter->title ?? 'General' }}</p>
                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                        <span class="flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ date('h:i A', strtotime($schedule->start_time)) }} - {{ date('h:i A', strtotime($schedule->end_time)) }}
                        </span>
                        @if($schedule->mode === 'online' && $schedule->meeting_link)
                            <a href="{{ $schedule->meeting_link }}" target="_blank" class="text-indigo-600 font-medium flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Join
                            </a>
                        @else
                            <span class="flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $schedule->venue }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
@endsection
