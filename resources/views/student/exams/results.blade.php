@extends('layouts.student_mobile')

@section('title', 'Results - ' . $exam->title)

@section('mobile-content')
<!-- Header -->
<div class="tb-header-gradient" style="background: linear-gradient(135deg, {{ $attempt->score >= $exam->passing_marks ? '#10b981' : '#ef4444' }} 0%, {{ $attempt->score >= $exam->passing_marks ? '#059669' : '#dc2626' }} 50%, {{ $attempt->score >= $exam->passing_marks ? '#047857' : '#b91c1c' }} 100%);">
    <a href="{{ route('student.exams.index') }}" class="flex items-center gap-2 text-white/80 mb-4">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        <span>Back to Exams</span>
    </a>
    <div class="text-center">
        <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
            @if($attempt->score >= $exam->passing_marks)
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                </svg>
            @else
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            @endif
        </div>
        <h1 class="text-2xl font-bold text-white">{{ $attempt->score >= $exam->passing_marks ? 'Congratulations!' : 'Keep Trying!' }}</h1>
        <p class="text-white/80 mt-1">{{ $exam->title }}</p>
    </div>
</div>

<!-- Score Card -->
<div class="px-4 -mt-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border {{ $attempt->score >= $exam->passing_marks ? 'border-green-200' : 'border-red-200' }}">
        <div class="text-center mb-4">
            <p class="text-gray-500 text-sm">Your Score</p>
            <p class="text-4xl font-bold {{ $attempt->score >= $exam->passing_marks ? 'text-green-600' : 'text-red-600' }}">
                {{ $attempt->score ?? 0 }}<span class="text-2xl text-gray-400">/{{ $exam->total_marks }}</span>
            </p>
            <p class="text-sm {{ $attempt->score >= $exam->passing_marks ? 'text-green-600' : 'text-red-600' }} font-medium mt-1">
                {{ $attempt->score >= $exam->passing_marks ? 'PASSED' : 'NOT PASSED' }}
            </p>
        </div>
        
        <div class="grid grid-cols-3 gap-4 text-center border-t border-gray-100 pt-4">
            <div>
                <p class="text-lg font-bold text-gray-900">{{ $attempt->answers->where('is_correct', true)->count() }}</p>
                <p class="text-xs text-green-600">Correct</p>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-900">{{ $attempt->answers->where('is_correct', false)->whereNotNull('selected_option_id')->count() }}</p>
                <p class="text-xs text-red-600">Wrong</p>
            </div>
            <div>
                <p class="text-lg font-bold text-gray-900">{{ $attempt->answers->whereNull('selected_option_id')->count() }}</p>
                <p class="text-xs text-gray-500">Skipped</p>
            </div>
        </div>
        
        @if($exam->duration_minutes)
        <div class="border-t border-gray-100 pt-4 mt-4 text-center">
            <p class="text-sm text-gray-500">
                Time Taken: <span class="font-medium text-gray-900">{{ $attempt->started_at->diffInMinutes($attempt->submitted_at) }} minutes</span>
            </p>
        </div>
        @endif
    </div>
</div>

<!-- Question Review -->
<div class="px-4 mt-6 pb-6">
    <h2 class="text-lg font-bold text-gray-900 mb-3">Question Review</h2>
    
    @php
        $questions = $exam->sections->flatMap(function($section) {
            return $section->questions;
        });
        if($questions->isEmpty()) {
            $questions = $exam->questions;
        }
    @endphp
    
    <div class="space-y-4">
        @foreach($questions as $index => $question)
        @php
            $answer = $attempt->answers->where('question_id', $question->id)->first();
            $isCorrect = $answer && $answer->is_correct;
            $isWrong = $answer && !$answer->is_correct && $answer->selected_option_id;
            $isSkipped = !$answer || !$answer->selected_option_id;
        @endphp
        <div class="bg-white rounded-2xl border {{ $isCorrect ? 'border-green-200' : ($isWrong ? 'border-red-200' : 'border-gray-200') }} p-4">
            <div class="flex items-start gap-3">
                <span class="w-8 h-8 {{ $isCorrect ? 'bg-green-100 text-green-700' : ($isWrong ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }} rounded-lg flex items-center justify-center font-bold flex-shrink-0">
                    {{ $index + 1 }}
                </span>
                <div class="flex-1">
                    <p class="font-medium text-gray-900">{{ $question->question_text }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $question->marks }} marks</p>
                    
                    <!-- Options Review -->
                    <div class="mt-3 space-y-2">
                        @foreach($question->options as $option)
                        @php
                            $isSelected = $answer && $answer->selected_option_id == $option->id;
                            $isCorrectOption = $option->is_correct;
                        @endphp
                        <div class="flex items-center gap-2 p-2 rounded-lg {{ $isSelected && $isCorrectOption ? 'bg-green-50' : ($isSelected && !$isCorrectOption ? 'bg-red-50' : ($isCorrectOption ? 'bg-green-50' : 'bg-gray-50')) }}">
                            <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0 {{ $isCorrectOption ? 'border-green-500' : ($isSelected ? 'border-red-500' : 'border-gray-300') }}">
                                @if($isCorrectOption)
                                    <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                @elseif($isSelected)
                                    <svg class="w-3 h-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                @endif
                            </span>
                            <span class="text-sm {{ $isCorrectOption ? 'text-green-700 font-medium' : ($isSelected ? 'text-red-700' : 'text-gray-600') }}">{{ $option->option_text }}</span>
                        </div>
                        @endforeach
                    </div>
                    
                    @if($question->explanation)
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm text-blue-800"><span class="font-medium">Explanation:</span> {{ $question->explanation }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Action Buttons -->
    <div class="mt-6 space-y-3">
        @if($exam->allow_multiple_attempts)
            @php
                $attemptsCount = \App\Models\ExamAttempt::where('student_id', Auth::id())->where('exam_id', $exam->id)->count();
                $canRetake = !$exam->max_attempts || $attemptsCount < $exam->max_attempts;
            @endphp
            @if($canRetake)
            <form action="{{ route('student.exams.start', $exam) }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-4 rounded-2xl font-bold text-white text-lg shadow-lg" style="background:linear-gradient(135deg,#7c3aed,#6d28d9)">
                    Attempt Again
                </button>
            </form>
            @endif
        @endif
        
        <a href="{{ route('student.exams.index') }}" class="block w-full py-4 rounded-2xl font-bold text-gray-700 bg-gray-100 text-center">
            Back to All Exams
        </a>
    </div>
</div>
@endsection
