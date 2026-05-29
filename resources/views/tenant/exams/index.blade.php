@extends('layouts.tenant')

@section('title', 'Exams - ' . $course->title)
@section('page-title', 'All Exams')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('tenant.courses.show', $course) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Course
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Exams: {{ $course->title }}</h1>
        </div>
        <a href="{{ route('tenant.curriculum.index', $course) }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New Exam
        </a>
    </div>

    <!-- Stats -->
    @php
        $totalExams = $exams->total();
        $publishedExams = $exams->where('status', 'published')->count();
        $draftExams = $exams->where('status', 'draft')->count();
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
            <p class="text-sm text-purple-600">Total Exams</p>
            <p class="text-2xl font-bold text-purple-900">{{ $totalExams }}</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
            <p class="text-sm text-green-600">Published</p>
            <p class="text-2xl font-bold text-green-900">{{ $publishedExams }}</p>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
            <p class="text-sm text-gray-600">Draft</p>
            <p class="text-2xl font-bold text-gray-900">{{ $draftExams }}</p>
        </div>
    </div>

    <!-- Exams List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        @if($exams->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($exams as $exam)
            <div class="p-6 hover:bg-gray-50 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 flex-wrap mb-2">
                            <h3 class="font-semibold text-gray-900 text-lg">{{ $exam->title }}</h3>
                            <span class="badge {{ $exam->status_badge }}">{{ ucfirst($exam->status) }}</span>
                        </div>
                        
                        <div class="flex items-center gap-4 text-sm text-gray-500 flex-wrap">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                {{ $exam->level_name }}
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 2h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $exam->total_questions }} questions
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                {{ $exam->total_marks }} marks
                            </span>
                            @if($exam->duration_minutes)
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $exam->duration_minutes }} min
                            </span>
                            @endif
                        </div>

                        @if($exam->start_time)
                        <p class="text-sm text-gray-500 mt-2">
                            Scheduled: {{ $exam->start_time->format('d M Y, h:i A') }}
                            @if($exam->end_time)
                                - {{ $exam->end_time->format('d M Y, h:i A') }}
                            @endif
                        </p>
                        @endif
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('tenant.exams.show', [$course, $exam]) }}" class="btn-secondary text-sm">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="p-4 border-t">
            {{ $exams->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Exams Created</h3>
            <p class="text-gray-500 mb-4">Create your first exam for this course.</p>
            <a href="{{ route('tenant.curriculum.index', $course) }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Exam
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
