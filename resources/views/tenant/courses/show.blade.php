@extends('layouts.tenant')

@section('title', $course->title)
@section('page-title', 'Course Details')

@section('page-content')
<div class="space-y-6">

    {{-- Header Bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('tenant.courses.index') }}" class="btn-secondary flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Courses
        </a>
        <div class="flex gap-2">
            <a href="{{ route('tenant.curriculum.index', $course) }}" class="btn-secondary">
                Manage Curriculum
            </a>
            <a href="{{ route('tenant.courses.edit', $course) }}" class="btn-primary">
                Edit Course
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Course Info Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-600 to-indigo-600 px-6 py-8 flex gap-5 items-center">
                    @if($course->thumbnail)
                        <img src="{{ $course->thumbnail_url }}" alt="" class="w-20 h-20 rounded-xl object-cover shadow">
                    @else
                        <div class="w-20 h-20 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-white">{{ $course->title }}</h1>
                        @if($course->description)
                            <p class="text-white/75 mt-1 text-sm">{{ $course->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="p-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Fees Type</p>
                        <p class="mt-1 font-bold text-gray-900">
                            @if($course->fees_type === 'monthly')
                                <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700 font-bold">📅 Monthly</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-violet-100 text-violet-700 font-bold">💳 One-Time</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">
                            {{ $course->fees_type === 'monthly' ? 'Monthly Fee' : 'Course Fee' }}
                        </p>
                        <p class="mt-1 font-bold text-gray-900 text-lg">₹{{ number_format($course->fees, 2) }}</p>
                    </div>
                    @if($course->fees_type === 'monthly' && $course->past_month_fee > 0)
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Past Month Fee</p>
                        <p class="mt-1 font-bold text-amber-600 text-lg">₹{{ number_format($course->past_month_fee, 2) }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Duration</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $course->duration ? preg_replace('/\b(\d+)\.\d+\b/', '$1', $course->duration) : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Status</p>
                        <span class="mt-1 inline-block badge {{ $course->status === 'active' ? 'badge-success' : ($course->status === 'draft' ? 'badge-warning' : 'badge-danger') }}">
                            {{ ucfirst($course->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Start Date</p>
                        <p class="mt-1 font-medium text-gray-900">
                            {{ $course->start_date ? $course->start_date->format('d M Y') : '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">End Date</p>
                        <p class="mt-1 font-medium text-gray-900">
                            {{ $course->end_date ? $course->end_date->format('d M Y') : '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-semibold tracking-wide">Created</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $course->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Teachers --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Assigned Teachers</h3>
                @forelse($course->teachers as $teacher)
                    <div class="flex items-center gap-3 py-2 border-b border-gray-100 last:border-0">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-sm">
                            {{ strtoupper(substr($teacher->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900 text-sm">{{ $teacher->name }}</p>
                            <p class="text-xs text-gray-500">{{ $teacher->email }}</p>
                        </div>
                        @if($teacher->pivot->is_primary)
                            <span class="ml-auto text-xs bg-violet-100 text-violet-700 px-2 py-0.5 rounded-full font-semibold">Primary</span>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-400">No teachers assigned.</p>
                @endforelse
            </div>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Stats --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-4">Overview</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Enrolled Students</span>
                        <span class="font-bold text-gray-900">{{ $course->enrollments->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Fees Collected</span>
                        <span class="font-bold text-green-600">₹{{ number_format($course->enrollments->sum('fees_paid'), 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Active Students</span>
                        <span class="font-bold text-blue-600">{{ $course->enrollments->where('enrollment_status', 'active')->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="font-semibold text-gray-900 mb-3">Quick Links</h3>
                <div class="space-y-2">
                    <a href="{{ route('tenant.curriculum.index', $course) }}"
                       class="flex items-center gap-2 text-sm text-violet-600 hover:text-violet-700 hover:bg-violet-50 rounded-lg px-3 py-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Manage Curriculum
                    </a>
                    <a href="{{ route('tenant.enrollments.index') }}?course_id={{ $course->id }}"
                       class="flex items-center gap-2 text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg px-3 py-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        View Enrollments
                    </a>
                    @if($course->fees_type === 'monthly')
                    <a href="{{ route('tenant.subscriptions.index') }}?course_id={{ $course->id }}"
                       class="flex items-center gap-2 text-sm text-amber-600 hover:text-amber-700 hover:bg-amber-50 rounded-lg px-3 py-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Manage Subscriptions
                    </a>
                    @endif
                    <a href="{{ route('tenant.courses.edit', $course) }}"
                       class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-700 hover:bg-gray-50 rounded-lg px-3 py-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Course
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- Enrolled Students Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Enrolled Students ({{ $course->enrollments->count() }})</h3>
            <a href="{{ route('tenant.enrollments.create') }}?course_id={{ $course->id }}" class="btn-primary text-sm">
                + Enroll Student
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th>Student</th>
                        <th>Enrollment Status</th>
                        <th>Payment Status</th>
                        <th>Fees Paid</th>
                        <th>Enrolled At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($course->enrollments as $enrollment)
                        <tr>
                            <td>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $enrollment->student->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $enrollment->student->email }}</p>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $enrollment->status_badge_class }}">
                                    {{ ucfirst($enrollment->enrollment_status) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $enrollment->payment_status_badge_class }}">
                                    {{ ucfirst($enrollment->payment_status) }}
                                </span>
                            </td>
                            <td class="font-medium">₹{{ number_format($enrollment->fees_paid, 2) }}</td>
                            <td class="text-sm text-gray-600">
                                {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('d M Y') : '—' }}
                            </td>
                            <td>
                                <a href="{{ route('tenant.enrollments.show', $enrollment) }}"
                                   class="text-blue-600 hover:underline text-sm font-medium">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400">No students enrolled yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
