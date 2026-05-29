@extends('layouts.tenant')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('page-content')
<!-- Stats Grid -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Students -->
    <div class="stat-card p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">Students</p>
                <h3 class="text-xl font-bold text-gray-900">{{ $stats['total_students'] }}</h3>
            </div>
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Active Courses -->
    <div class="stat-card p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">Courses</p>
                <h3 class="text-xl font-bold text-gray-900">{{ $stats['active_courses'] }}</h3>
            </div>
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Teachers -->
    <div class="stat-card p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">Teachers</p>
                <h3 class="text-xl font-bold text-gray-900">{{ $stats['total_teachers'] }}</h3>
            </div>
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Pending Admissions -->
    <div class="stat-card p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 mb-1">Pending</p>
                <h3 class="text-xl font-bold {{ $stats['pending_admissions'] > 0 ? 'text-orange-600' : 'text-gray-900' }}">
                    {{ $stats['pending_admissions'] }}
                </h3>
            </div>
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Enrollments -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-4 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Recent Admissions</h3>
            <a href="{{ route('tenant.enrollments.index') }}" class="text-sm text-blue-600 hover:text-blue-700">View all</a>
        </div>
        <div class="divide-y divide-gray-200">
            @forelse($recentEnrollments as $enrollment)
                <div class="px-4 py-3 flex items-center justify-between hover:bg-gray-50">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <span class="text-gray-600 text-xs font-medium">{{ substr($enrollment->student->name, 0, 2) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ $enrollment->student->name }}</p>
                            <p class="text-xs text-gray-500">{{ $enrollment->course->title }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="badge badge-{{ $enrollment->status_badge_class }}">
                            {{ ucfirst($enrollment->enrollment_status) }}
                        </span>
                        <span class="text-xs text-gray-400">{{ $enrollment->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-gray-500 text-sm">
                    No recent admissions
                </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions & Notices -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-4 py-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900">Quick Actions</h3>
            </div>
            <div class="p-3 space-y-1">
                <a href="{{ route('tenant.students.create') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700">Add Student</span>
                </a>

                <a href="{{ route('tenant.teachers.create') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700">Add Teacher</span>
                </a>

                <a href="{{ route('tenant.courses.create') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700">Create Course</span>
                </a>

                <a href="{{ route('tenant.notices.create') }}" class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-gray-700">Post Notice</span>
                </a>
            </div>
        </div>

        <!-- Recent Notices -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-4 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Notices</h3>
                <a href="{{ route('tenant.notices.index') }}" class="text-sm text-blue-600">View all</a>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentNotices as $notice)
                    <div class="px-4 py-3 hover:bg-gray-50">
                        <div class="flex items-start gap-2">
                            <span class="badge badge-{{ $notice->type_badge_class }} mt-0.5">
                                {{ ucfirst($notice->type) }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 text-sm truncate">{{ $notice->title }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ \Illuminate\Support\Str::limit(strip_tags($notice->content ?? ''), 60) }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-6 text-center text-gray-500 text-sm">
                        No active notices
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
