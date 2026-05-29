@extends('layouts.student_mobile')

@section('title', $exam->title)

@section('mobile-content')
<!-- Header -->
<div class="tb-header-gradient" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 50%, #5b21b6 100%);">
    <a href="{{ url()->previous() }}" class="flex items-center gap-2 text-white/80 mb-4">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        <span>Back</span>
    </a>
    <h1 class="text-xl font-bold text-white">{{ $exam->title }}</h1>
    <p class="text-white/70 text-sm mt-1">{{ $exam->course->title }}</p>
</div>

<!-- Exam Stats -->
<div class="px-4 -mt-4">
    <div class="bg-white rounded-2xl shadow-lg p-4 border border-purple-100">
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-2xl font-bold text-purple-600">{{ $exam->total_questions }}</p>
                <p class="text-xs text-gray-500">Questions</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-purple-600">{{ $exam->total_marks }}</p>
                <p class="text-xs text-gray-500">Total Marks</p>
            </div>
            <div>
                <p class="text-2xl font-bold text-purple-600">{{ $exam->duration_minutes ?? '∞' }}</p>
                <p class="text-xs text-gray-500">Minutes</p>
            </div>
        </div>
        @if($exam->passing_marks > 0)
        <div class="mt-3 pt-3 border-t border-gray-100 text-center">
            <p class="text-sm text-gray-600">Passing Marks: <span class="font-bold text-green-600">{{ $exam->passing_marks }}</span></p>
        </div>
        @endif
    </div>
</div>

<!-- Instructions -->
<div class="px-4 mt-4">
    <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
        <h3 class="font-bold text-blue-900 mb-2 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Instructions
        </h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li>• Read each question carefully before answering</li>
            <li>• You can navigate between questions</li>
            @if($exam->duration_minutes)
                <li>• Exam will auto-submit when time expires</li>
            @endif
            @if($exam->shuffle_questions)
                <li>• Questions are shuffled randomly</li>
            @endif
            @if($exam->allow_multiple_attempts)
                <li>• Multiple attempts allowed{{ $exam->max_attempts ? ' (max ' . $exam->max_attempts . ')' : '' }}</li>
            @else
                <li>• Only one attempt allowed</li>
            @endif
        </ul>
    </div>
</div>

<!-- Previous Attempts -->
@if($existingAttempts->count() > 0)
<div class="px-4 mt-4">
    <h3 class="font-bold text-gray-900 mb-2">📊 Your Previous Attempts</h3>
    <div class="space-y-2">
        @foreach($existingAttempts as $attempt)
        <div class="bg-white rounded-xl border border-gray-200 p-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $attempt->status === 'completed' ? ($attempt->score >= $exam->passing_marks ? 'bg-green-100' : 'bg-red-100') : 'bg-yellow-100' }}">
                    @if($attempt->status === 'completed')
                        @if($attempt->score >= $exam->passing_marks)
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        @endif
                    @else
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                </div>
                <div>
                    <p class="font-medium text-sm">{{ $attempt->created_at->format('d M Y, h:i A') }}</p>
                    @if($attempt->status === 'completed' && $attempt->score !== null)
                        <p class="text-xs {{ $attempt->score >= $exam->passing_marks ? 'text-green-600' : 'text-red-600' }}">
                            Score: {{ $attempt->score }}/{{ $exam->total_marks }}
                        </p>
                    @else
                        <p class="text-xs text-yellow-600">In Progress</p>
                    @endif
                </div>
            </div>
            @if($attempt->status === 'completed')
                <a href="{{ route('student.exams.results', ['exam' => $exam, 'attempt' => $attempt]) }}" class="text-sm text-purple-600 font-medium">View Result</a>
            @else
                <a href="{{ route('student.exams.attempt', ['exam' => $exam, 'attempt' => $attempt]) }}" class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-sm font-medium">Continue</a>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Start Button -->
<div class="px-4 mt-6 pb-6">
    @if($canAttempt)
        <form action="{{ route('student.exams.start', $exam) }}" method="POST">
            @csrf
            <button type="submit" class="w-full py-4 rounded-2xl font-bold text-white text-lg shadow-lg" style="background:linear-gradient(135deg,#7c3aed,#6d28d9)">
                {{ $existingAttempts->count() > 0 ? 'Attempt Again' : 'Start Exam' }}
            </button>
        </form>
    @else
        <div class="bg-gray-100 rounded-2xl p-4 text-center">
            <p class="text-gray-600">{{ $attemptMessage }}</p>
        </div>
    @endif
</div>
@endsection
