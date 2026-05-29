@extends('layouts.teacher')

@section('title', 'My Courses')
@section('page-title', 'My Courses')

@section('page-content')
<div class="space-y-6">
    <!-- Stats -->
    <div class="grid grid-cols-2 gap-4">
        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">My Courses</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $courses->count() }}</h3>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Total Students</p>
                    <h3 class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</h3>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($courses as $course)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Course Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-white">{{ $course->title }}</h3>
                        <span class="badge bg-white/20 text-white border-0">
                            {{ $course->enrollments_count }} Students
                        </span>
                    </div>
                </div>

                <div class="p-4">
                    <!-- Course Info -->
                    <p class="text-gray-600 text-sm mb-4">{{ $course->description }}</p>
                    
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-4">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $course->duration ?? 'N/A' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            ₹{{ number_format($course->fees) }}
                        </span>
                    </div>

                    <!-- Enrolled Students -->
                    @if($course->enrollments->count() > 0)
                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-xs font-medium text-gray-500 uppercase mb-2">Enrolled Students</p>
                            <div class="flex flex-wrap gap-1">
                                @foreach($course->enrollments->take(8) as $enrollment)
                                    <span class="inline-flex items-center px-2 py-1 bg-gray-100 rounded text-xs text-gray-700">
                                        {{ $enrollment->student->name }}
                                    </span>
                                @endforeach
                                @if($course->enrollments->count() > 8)
                                    <span class="text-xs text-gray-500 self-center">+{{ $course->enrollments->count() - 8 }} more</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="border-t border-gray-100 pt-3">
                            <p class="text-sm text-gray-400 italic">No students enrolled yet</p>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 mt-4 pt-3 border-t border-gray-100">
                        <a href="{{ route('teacher.courses.show', $course) }}" class="btn-primary text-sm flex-1 justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Manage Course
                        </a>
                        <button onclick="alert('Attendance feature coming soon!')" class="btn-secondary text-sm flex-1 justify-center opacity-70">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Attendance
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No courses assigned</h3>
                <p class="text-gray-500">Contact your administrator to get assigned to courses.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
