@extends('layouts.teacher')

@section('title', 'Teacher Dashboard')
@section('page-title', 'Dashboard')

@section('page-content')
        <!-- Stats -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="stat-card p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">My Courses</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $stats['assigned_courses'] }}</h3>
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
                        <p class="text-xs text-gray-500 mb-1">My Students</p>
                        <h3 class="text-2xl font-bold text-gray-900">{{ $stats['total_students'] }}</h3>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Courses -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
            <div class="px-4 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">My Courses</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($assignedCourses as $course)
                    <div class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $course->title }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ $course->description }}</p>
                                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $course->duration }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $course->enrollments_count ?? 0 }} students
                                    </span>
                                </div>
                            </div>
                            <span class="badge badge-success">Active</span>
                        </div>
                        
                        <!-- Enrolled Students Preview -->
                        @if($course->enrollments->count() > 0)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <p class="text-xs text-gray-500 mb-2">Enrolled Students:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($course->enrollments->take(5) as $enrollment)
                                        <span class="inline-flex items-center px-2 py-1 bg-gray-100 rounded text-xs text-gray-700">
                                            {{ $enrollment->student->name }}
                                        </span>
                                    @endforeach
                                    @if($course->enrollments->count() > 5)
                                        <span class="text-xs text-gray-500">+{{ $course->enrollments->count() - 5 }} more</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <!-- Action Button -->
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <a href="{{ route('teacher.courses.show', $course) }}" class="btn-primary text-sm w-full justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Manage Course
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-gray-500">
                        No courses assigned yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Notices -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-4 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Notices</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($notices as $notice)
                    <div class="px-4 py-3">
                        <div class="flex items-start gap-2">
                            <span class="badge badge-{{ $notice->type_badge_class }} mt-0.5">
                                {{ ucfirst($notice->type) }}
                            </span>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 text-sm">{{ $notice->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $notice->excerpt }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-6 text-center text-gray-500 text-sm">
                        No notices available
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('teacher.courses') }}" class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 hover:border-blue-300 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">My Courses</p>
                        <p class="text-xs text-gray-500">View all courses</p>
                    </div>
                </div>
            </a>

            <button onclick="alert('Attendance feature coming soon!')" class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 opacity-70 cursor-not-allowed">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-600">Attendance</p>
                        <p class="text-xs text-gray-400">Coming soon</p>
                    </div>
                </div>
            </button>

            <button onclick="alert('Materials feature coming soon!')" class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 opacity-70 cursor-not-allowed">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-600">Materials</p>
                        <p class="text-xs text-gray-400">Coming soon</p>
                    </div>
                </div>
            </button>

            <button onclick="alert('Exams feature coming soon!')" class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 opacity-70 cursor-not-allowed">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-600">Exams</p>
                        <p class="text-xs text-gray-400">Coming soon</p>
                    </div>
                </div>
            </button>
        </div>
@endsection
