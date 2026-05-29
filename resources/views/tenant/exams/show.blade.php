@extends('layouts.tenant')

@section('title', $exam->title)
@section('page-title', 'Exam Details')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('tenant.curriculum.index', $course) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Curriculum
            </a>
            <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }}</h1>
            <div class="flex items-center gap-3 mt-1">
                <span class="badge {{ $exam->status_badge }}">{{ ucfirst($exam->status) }}</span>
                <span class="text-sm text-gray-500">{{ $exam->level_name }}</span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($exam->status === 'draft')
                <form action="{{ route('tenant.exams.publish', [$course, $exam]) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="btn-success inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Publish Exam
                    </button>
                </form>
            @endif
            <a href="{{ route('tenant.exams.questions.create', [$course, $exam]) }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Questions
            </a>
            <form action="{{ route('tenant.exams.destroy', [$course, $exam]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this exam? All questions and attempts will be lost!')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger inline-flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">
        {{ session('error') }}
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total Questions</p>
            <p class="text-2xl font-bold text-gray-900">{{ $exam->total_questions }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Total Marks</p>
            <p class="text-2xl font-bold text-gray-900">{{ $exam->total_marks }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Passing Marks</p>
            <p class="text-2xl font-bold text-gray-900">{{ $exam->passing_marks }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Duration</p>
            <p class="text-2xl font-bold text-gray-900">{{ $exam->duration_minutes ? $exam->duration_minutes . ' min' : 'No limit' }}</p>
        </div>
    </div>

    <!-- Exam Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam Configuration</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Template:</span>
                <span class="font-medium capitalize">{{ $exam->template }}</span>
            </div>
            <div>
                <span class="text-gray-500">Shuffle Questions:</span>
                <span class="font-medium">{{ $exam->shuffle_questions ? 'Yes' : 'No' }}</span>
            </div>
            <div>
                <span class="text-gray-500">Show Result Immediately:</span>
                <span class="font-medium">{{ $exam->show_result_immediately ? 'Yes' : 'No' }}</span>
            </div>
            <div>
                <span class="text-gray-500">Multiple Attempts:</span>
                <span class="font-medium">{{ $exam->allow_multiple_attempts ? ($exam->max_attempts ? $exam->max_attempts . ' attempts' : 'Unlimited') : 'No' }}</span>
            </div>
            @if($exam->start_time)
            <div>
                <span class="text-gray-500">Start Time:</span>
                <span class="font-medium">{{ $exam->start_time->format('d M Y, h:i A') }}</span>
            </div>
            @endif
            @if($exam->end_time)
            <div>
                <span class="text-gray-500">End Time:</span>
                <span class="font-medium">{{ $exam->end_time->format('d M Y, h:i A') }}</span>
            </div>
            @endif
        </div>

        @if($exam->description)
        <div class="mt-4 pt-4 border-t">
            <p class="text-gray-500 text-sm mb-1">Description:</p>
            <p class="text-gray-900">{{ $exam->description }}</p>
        </div>
        @endif
    </div>

    <!-- Sections & Questions -->
    @if($exam->sections->count() > 0)
        @foreach($exam->sections as $section)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $section->title }}</h3>
                    @if($section->description)
                        <p class="text-sm text-gray-500">{{ $section->description }}</p>
                    @endif
                </div>
                <span class="text-sm text-purple-600 font-medium">
                    {{ $section->questions->count() }} questions
                    · +{{ $section->marks_per_question }} marks
                    @if($section->negative_marks_per_question > 0)
                        / -{{ $section->negative_marks_per_question }}
                    @endif
                </span>
            </div>

            @if($section->questions->count() > 0)
            <div class="space-y-2">
                @foreach($section->questions as $question)
                <div class="border border-gray-100 rounded-lg p-3 bg-gray-50">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-purple-100 text-purple-700 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                            {{ $loop->iteration }}
                        </span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $question->question_text }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                <span class="text-xs text-gray-500">{{ $question->marks }} marks</span>
                                @if($question->negative_marks > 0)
                                    <span class="text-xs text-red-500">-{{ $question->negative_marks }} wrong</span>
                                @endif
                                <button type="button" onclick="toggleQuestionDetails('section-q-{{ $section->id }}-{{ $question->id }}')" class="text-xs text-blue-600 hover:text-blue-800 font-medium ml-2" id="btn-section-q-{{ $section->id }}-{{ $question->id }}">
                                    Show Details
                                </button>
                            </div>

                            <!-- Options (Hidden by default) -->
                            <div id="details-section-q-{{ $section->id }}-{{ $question->id }}" class="hidden mt-3 ml-2 space-y-1">
                                @foreach($question->options as $option)
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="w-4 h-4 rounded-full {{ $option->is_correct ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                        @if($option->is_correct)
                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="{{ $option->is_correct ? 'font-medium text-green-700' : 'text-gray-600' }}">{{ $option->option_text }}</span>
                                </div>
                                @endforeach
                                @if($question->explanation)
                                <div class="mt-2 text-sm text-gray-600 bg-blue-50 p-2 rounded">
                                    <span class="font-medium">Explanation:</span> {{ $question->explanation }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">No questions in this section yet.</p>
            @endif
        </div>
        @endforeach
    @else
        <!-- Questions without sections -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Questions ({{ $exam->questions->count() }})</h3>
            
            @if($exam->questions->count() > 0)
            <div class="space-y-2">
                @foreach($exam->questions as $question)
                <div class="border border-gray-100 rounded-lg p-3 bg-gray-50">
                    <div class="flex items-start gap-3">
                        <span class="w-6 h-6 bg-purple-100 text-purple-700 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0">
                            {{ $loop->iteration }}
                        </span>
                        <div class="flex-1">
                            <p class="font-medium text-gray-900">{{ $question->question_text }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs bg-gray-200 text-gray-700 px-2 py-0.5 rounded">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                <span class="text-xs text-gray-500">{{ $question->marks }} marks</span>
                                @if($question->negative_marks > 0)
                                    <span class="text-xs text-red-500">-{{ $question->negative_marks }} wrong</span>
                                @endif
                                <button type="button" onclick="toggleQuestionDetails('q-{{ $question->id }}')" class="text-xs text-blue-600 hover:text-blue-800 font-medium ml-2" id="btn-q-{{ $question->id }}">
                                    Show Details
                                </button>
                            </div>

                            <!-- Options (Hidden by default) -->
                            <div id="details-q-{{ $question->id }}" class="hidden mt-3 ml-2 space-y-1">
                                @foreach($question->options as $option)
                                <div class="flex items-center gap-2 text-sm">
                                    <span class="w-4 h-4 rounded-full {{ $option->is_correct ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                                        @if($option->is_correct)
                                            <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="{{ $option->is_correct ? 'font-medium text-green-700' : 'text-gray-600' }}">{{ $option->option_text }}</span>
                                </div>
                                @endforeach
                                @if($question->explanation)
                                <div class="mt-2 text-sm text-gray-600 bg-blue-50 p-2 rounded">
                                    <span class="font-medium">Explanation:</span> {{ $question->explanation }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500">No questions added yet.</p>
                <a href="{{ route('tenant.exams.questions.create', [$course, $exam]) }}" class="btn-primary inline-flex items-center gap-2 mt-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Questions
                </a>
            </div>
            @endif
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function toggleQuestionDetails(questionId) {
    const detailsDiv = document.getElementById('details-' + questionId);
    const btn = document.getElementById('btn-' + questionId);
    
    if (detailsDiv.classList.contains('hidden')) {
        detailsDiv.classList.remove('hidden');
        btn.textContent = 'Hide Details';
    } else {
        detailsDiv.classList.add('hidden');
        btn.textContent = 'Show Details';
    }
}
</script>
@endpush
