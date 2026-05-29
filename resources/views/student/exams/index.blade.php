@extends('layouts.student_mobile')

@section('title', 'My Exams')

@section('mobile-content')
<!-- Header -->
<div class="tb-header-gradient" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 50%, #5b21b6 100%);">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-sm text-white/80">Test Your Knowledge</p>
            <h1 class="text-2xl font-bold text-white">My Exams</h1>
        </div>
        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
        </div>
    </div>
</div>

<!-- Quick Stats -->
@php
    $availableCount = $availableExams->count();
    $completedCount = $myAttempts->where('status', 'completed')->count();
@endphp
<div class="tb-stats-row -mt-6 mb-4">
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number text-purple-600">{{ $availableCount }}</p>
        <p class="tb-stat-label">Available</p>
    </div>
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number text-green-600">{{ $completedCount }}</p>
        <p class="tb-stat-label">Completed</p>
    </div>
</div>

<!-- Available Exams -->
@if($availableExams->count() > 0)
<div class="px-4 mb-6">
    <h2 class="text-lg font-bold text-gray-900 mb-3">📝 Available Exams</h2>
    <div class="space-y-3">
        @foreach($availableExams as $exam)
        @php
            $isEnded = $exam->end_time && $exam->end_time < now();
        @endphp
        @if($isEnded)
        <div class="block bg-white rounded-2xl border border-gray-200 shadow-sm p-4 opacity-60">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 bg-gray-100">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-900 truncate">{{ $exam->title }}</p>
                    <p class="text-xs text-gray-500">{{ $exam->course->title }}</p>
                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                        <span>{{ $exam->total_questions }} questions</span>
                        <span>{{ $exam->total_marks }} marks</span>
                        @if($exam->duration_minutes)
                            <span>{{ $exam->duration_minutes }} min</span>
                        @endif
                    </div>
                </div>
                <span class="flex-shrink-0 px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded-lg">Ended</span>
            </div>
        </div>
        @else
        <a href="{{ route('student.exams.show', $exam) }}" class="block bg-white rounded-2xl border border-purple-200 shadow-sm p-4 hover:border-purple-400 transition-colors">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#e9d5ff,#d8b4fe)">
                    <svg class="w-6 h-6" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-900 truncate">{{ $exam->title }}</p>
                    <p class="text-xs text-gray-500">{{ $exam->course->title }}</p>
                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500">
                        <span>{{ $exam->total_questions }} questions</span>
                        <span>{{ $exam->total_marks }} marks</span>
                        @if($exam->duration_minutes)
                            <span>{{ $exam->duration_minutes }} min</span>
                        @endif
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>
        @endif
        @endforeach
    </div>
</div>
@endif

<!-- My Attempts -->
@if($myAttempts->count() > 0)
<div class="px-4 mb-6">
    <h2 class="text-lg font-bold text-gray-900 mb-3">📊 My Results</h2>
    <div class="space-y-3">
        @foreach($myAttempts as $attempt)
        <a href="{{ route('student.exams.results', ['exam' => $attempt->exam, 'attempt' => $attempt]) }}" class="block bg-white rounded-2xl border border-gray-200 shadow-sm p-4 hover:border-gray-400 transition-colors">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 {{ $attempt->score >= $attempt->exam->passing_marks ? 'bg-green-100' : 'bg-red-100' }}">
                    <svg class="w-6 h-6 {{ $attempt->score >= $attempt->exam->passing_marks ? 'text-green-600' : 'text-red-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($attempt->score >= $attempt->exam->passing_marks)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        @endif
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-900 truncate">{{ $attempt->exam->title }}</p>
                    <p class="text-xs text-gray-500">{{ $attempt->exam->course->title }}</p>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="text-sm font-bold {{ $attempt->score >= $attempt->exam->passing_marks ? 'text-green-600' : 'text-red-600' }}">
                            {{ $attempt->score ?? 0 }}/{{ $attempt->exam->total_marks }}
                        </span>
                        <span class="text-xs text-gray-400">
                            {{ $attempt->created_at->format('d M Y') }}
                        </span>
                    </div>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

@if($availableExams->count() === 0 && $myAttempts->count() === 0)
<div class="text-center py-12 px-4">
    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
    </svg>
    <p class="text-gray-500 text-lg">No exams available yet.</p>
    <p class="text-sm text-gray-400 mt-2">Check back later for new exams!</p>
</div>
@endif
@endsection
