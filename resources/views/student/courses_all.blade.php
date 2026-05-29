@extends('layouts.student_mobile')

@section('title', 'Explore Courses')

@section('mobile-content')
<!-- Header with Gradient -->
<div class="tb-header-gradient" style="background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 50%, #4c1d95 100%);">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-sm text-white/80">Discover New</p>
            <h1 class="text-2xl font-bold text-white">Explore Courses</h1>
        </div>
        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
@php
    $monthlyCount = $availableCourses->where('fees_type', 'monthly')->count();
    $oneTimeCount = $availableCourses->where('fees_type', 'one_time')->count();
    $totalCount = $availableCourses->count();
@endphp
<div class="tb-stats-row -mt-6 mb-4">
    <div class="tb-stat-card shadow-lg {{ $totalCount > 0 ? 'ring-2 ring-violet-400' : '' }}">
        <p class="tb-stat-number text-violet-600">{{ $totalCount }}</p>
        <p class="tb-stat-label">Available</p>
    </div>
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number text-blue-600">{{ $monthlyCount }}</p>
        <p class="tb-stat-label">Monthly</p>
    </div>
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number text-purple-600">{{ $oneTimeCount }}</p>
        <p class="tb-stat-label">One-Time</p>
    </div>
</div>

<div class="px-4 pb-6">

    @if($availableCourses->count() > 0)
        @foreach($availableCourses as $course)
        @php $isMonthly = $course->fees_type === 'monthly'; @endphp

        <!-- Course Card -->
        <div class="tb-course-card mb-4">
            {{-- Header with gradient --}}
            <div class="h-24 flex items-center justify-center relative {{ $isMonthly ? 'bg-gradient-to-br from-blue-500 to-blue-700' : 'bg-gradient-to-br from-violet-500 to-violet-700' }}">
                <svg class="w-16 h-16 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <span class="absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-bold {{ $isMonthly ? 'bg-blue-100 text-blue-700' : 'bg-violet-100 text-violet-700' }}">
                    {{ $isMonthly ? 'Monthly' : 'One-Time' }}
                </span>
            </div>
            
            {{-- Body --}}
            <div class="p-4">
                <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $course->title }}</h3>
                
                @if($course->description)
                    <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ $course->description }}</p>
                @endif

                @if($course->teachers->count() > 0)
                    <div class="flex items-center gap-2 mb-3 text-sm text-gray-600">
                        <div class="w-6 h-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                            {{ strtoupper(substr($course->teachers->first()->name,0,1)) }}
                        </div>
                        <span>{{ $course->teachers->first()->name }}</span>
                    </div>
                @endif

                @if($course->duration)
                    <div class="flex items-center gap-2 mb-3 text-xs text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ preg_replace('/\b(\d+)\.\d+\b/', '$1', $course->duration) }}
                    </div>
                @endif

                @if($course->start_date || $course->end_date)
                    <div class="flex flex-wrap gap-2 mb-3">
                        @if($course->start_date)
                            <span class="px-2 py-1 rounded-lg bg-green-50 text-green-700 text-xs font-medium">
                                Starts {{ $course->start_date->format('d M Y') }}
                            </span>
                        @endif
                        @if($course->end_date)
                            <span class="px-2 py-1 rounded-lg bg-red-50 text-red-700 text-xs font-medium">
                                Ends {{ $course->end_date->format('d M Y') }}
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Fee Section --}}
                <div class="bg-gray-50 rounded-xl p-3 mb-3">
                    @if($isMonthly)
                        <div class="flex items-end justify-between mb-2">
                            <div>
                                <p class="text-2xl font-bold text-blue-600">₹{{ number_format($course->fees) }}</p>
                                <p class="text-xs text-gray-500">per month</p>
                            </div>
                            <span class="text-xs text-blue-600 font-medium">30-day access</span>
                        </div>
                        
                        @if($course->past_month_fee > 0)
                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <p class="text-xs text-amber-600 font-medium mb-1">Past Month: ₹{{ number_format($course->past_month_fee) }}</p>
                                <p class="text-xs text-gray-400">Pay to unlock older content</p>
                            </div>
                        @endif
                    @else
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-2xl font-bold text-violet-600">₹{{ number_format($course->fees) }}</p>
                                <p class="text-xs text-gray-500">one-time payment</p>
                            </div>
                            <span class="px-2 py-1 rounded-lg bg-violet-100 text-violet-700 text-xs font-bold">Lifetime</span>
                        </div>
                    @endif
                </div>

                {{-- Enroll Button --}}
                <a href="{{ route('student.payments.create', ['course_id' => $course->id, 'type' => 'enrollment']) }}" 
                   class="tb-btn-primary w-full {{ $isMonthly ? 'bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Enroll Now
                </a>
            </div>
        </div>
        @endforeach

        {{-- Pagination --}}
        <div class="mt-6">{{ $availableCourses->links() }}</div>

    @else
        <div class="tb-card text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <p class="text-gray-700 font-bold text-lg mb-2">No Courses Available</p>
            <p class="text-gray-500 text-sm">Please check back later or contact administration.</p>
        </div>
    @endif

    {{-- Already enrolled note --}}
    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4 flex gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <p class="text-sm font-bold text-blue-800 mb-1">Already enrolled?</p>
            <p class="text-sm text-blue-600">Go to <a href="{{ route('student.courses') }}" class="underline font-bold">My Courses</a> to access your content.</p>
        </div>
    </div>

</div>
@endsection
