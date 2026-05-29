@extends('layouts.student_mobile')

@section('title', 'Exam in Progress - ' . $exam->title)

@push('styles')
<style>
    .question-nav-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
    }
    .question-nav-btn.answered {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }
    .question-nav-btn.unanswered {
        background: #f3f4f6;
        color: #6b7280;
        border: 2px solid #e5e7eb;
    }
    .question-nav-btn.current {
        box-shadow: 0 0 0 3px #7c3aed;
        transform: scale(1.1);
    }
    .option-card {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .option-card:hover {
        border-color: #7c3aed;
        background: #faf5ff;
    }
    .option-card.selected {
        border-color: #7c3aed;
        background: linear-gradient(135deg, #faf5ff, #f3e8ff);
    }
    /* Custom slim colorful scrollbar */
    .questions-scroll::-webkit-scrollbar {
        width: 6px;
    }
    .questions-scroll::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 3px;
    }
    .questions-scroll::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, #7c3aed, #a855f7);
        border-radius: 3px;
    }
    .questions-scroll::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(180deg, #6d28d9, #9333ea);
    }
    /* Firefox */
    .questions-scroll {
        scrollbar-width: thin;
        scrollbar-color: #7c3aed #f3f4f6;
    }
</style>
@endpush

@section('mobile-content')
<!-- Fixed Header -->
<div class="fixed top-0 left-0 right-0 bg-white border-b border-gray-200 z-50 shadow-sm">
    <div class="flex items-center justify-between p-4">
        <h1 class="font-bold text-gray-900 truncate flex-1">{{ $exam->title }}</h1>
        @if($timeRemaining !== null)
        <div class="flex items-center gap-2 text-red-600 font-bold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span id="timer">{{ floor((int)$timeRemaining / 60) }}:{{ sprintf('%02d', (int)$timeRemaining % 60) }}</span>
        </div>
        @endif
    </div>
    
    <!-- Question Navigation -->
    <div class="px-4 pb-3 overflow-x-auto">
        <div class="flex gap-2" style="min-width: max-content;">
            @php
                // Get questions from sections
                $sectionQuestions = $exam->sections->flatMap(function($section) {
                    return $section->questions ?? collect();
                });
                // Get standalone questions
                $standaloneQuestions = $exam->questions ?? collect();
                // Combine both
                $questions = $sectionQuestions->merge($standaloneQuestions);
                $hasSections = $exam->sections->count();
                $sectionQCount = $sectionQuestions->count();
                $standaloneQCount = $standaloneQuestions->count();
            @endphp
            <!-- Debug: Sections: {{ $hasSections }}, Section Qs: {{ $sectionQCount }}, Standalone Qs: {{ $standaloneQCount }}, Total: {{ $questions->count() }} -->
            @if($questions->count() > 0)
                @foreach($questions as $index => $q)
                    <button type="button" 
                            onclick="showQuestion({{ $index }})" 
                            class="question-nav-btn {{ isset($savedAnswers[$q->id]) ? 'answered' : 'unanswered' }}"
                            id="nav-{{ $index }}">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            @else
                <p class="text-sm text-gray-500">No questions available</p>
            @endif
        </div>
    </div>
</div>

<!-- Scrollable Content Area -->
<div class="questions-scroll overflow-y-auto px-4 pt-4" style="height: calc(100vh - 160px); margin-top: 100px;" id="questions-container">
    <!-- Debug: Total questions: {{ $questions->count() }} -->
    @if($questions->count() > 0)
        @foreach($questions as $index => $question)
    <div class="question-card {{ $index > 0 ? 'hidden' : '' }}" data-index="{{ $index }}" data-question-id="{{ $question->id }}">
        <div class="bg-white rounded-2xl border border-gray-200 p-4 mb-4">
            <div class="flex items-start gap-3">
                <span class="w-8 h-8 bg-purple-100 text-purple-700 rounded-lg flex items-center justify-center font-bold flex-shrink-0">
                    {{ $index + 1 }}
                </span>
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-gray-900 text-lg leading-relaxed break-words">{{ $question->question_text }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $question->marks }} marks</p>
                </div>
            </div>
        </div>

        <!-- Options -->
        <div class="space-y-3 pr-2" id="options-{{ $index }}">
            @foreach($question->options as $option)
            <div class="option-card {{ isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $option->id ? 'selected' : '' }}" 
                 onclick="selectOption({{ $index }}, {{ $question->id }}, {{ $option->id }})"
                 data-option-id="{{ $option->id }}">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 rounded-full border-2 border-gray-300 flex items-center justify-center flex-shrink-0 option-circle">
                        <span class="w-3 h-3 rounded-full bg-purple-600 hidden option-dot"></span>
                    </span>
                    <span class="text-gray-700 break-words">{{ $option->option_text }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-6 mb-20 pb-4">
            @if($index > 0)
                <button type="button" onclick="showQuestion({{ $index - 1 }})" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-medium">
                    ← Previous
                </button>
            @else
                <div></div>
            @endif
            
            @if($index < $questions->count() - 1)
                <button type="button" onclick="showQuestion({{ $index + 1 }})" class="px-4 py-2 bg-purple-100 text-purple-700 rounded-xl font-medium">
                    Next →
                </button>
            @else
                <button type="button" onclick="console.log('Last question Submit clicked'); submitExam(); return false;" class="px-6 py-3 bg-green-600 text-white rounded-xl font-bold shadow-lg hover:bg-green-700 z-50 relative" style="min-width: 120px;">
                    Submit Exam
                </button>
            @endif
        </div>
    </div>
        @endforeach
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <p class="text-gray-500">No questions available for this exam.</p>
        </div>
    @endif
</div>

<!-- Fixed bottom bar - only shows answered count -->
<div class="fixed bottom-16 left-0 right-0 bg-white border-t border-gray-200 p-4 z-40">
    <div class="flex items-center justify-center">
        <div class="text-sm text-gray-500">
            <span id="answered-count">{{ count($savedAnswers) }}</span> / {{ $questions->count() }} answered
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div id="submit-modal" class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-sm w-full">
        <div class="text-center">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Submit Exam?</h3>
            <p class="text-gray-500 mb-6">You have answered <span id="modal-answered">0</span> out of {{ $questions->count() }} questions. Are you sure you want to submit?</p>
            <div class="flex gap-3">
                <button type="button" onclick="closeSubmitModal()" class="flex-1 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium">
                    Cancel
                </button>
                <form action="{{ route('student.exams.submit', ['exam' => $exam, 'attempt' => $attempt]) }}" method="POST" class="flex-1" id="submit-form">
                    @csrf
                    <input type="submit" value="Yes, Submit Exam" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold shadow-lg cursor-pointer border-0" style="min-height: 48px;">
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentQuestion = 0;
const totalQuestions = {{ $questions->count() }};
const savedAnswers = @json($savedAnswers);
console.log('Exam attempt page loaded. Total questions:', totalQuestions);
console.log('Question cards found:', document.querySelectorAll('.question-card').length);

function showQuestion(index) {
    console.log('showQuestion called with index:', index);
    // Hide all questions
    document.querySelectorAll('.question-card').forEach((card, i) => {
        card.classList.add('hidden');
        console.log('Hiding question', i);
    });
    
    // Show current question
    const targetCard = document.querySelector(`.question-card[data-index="${index}"]`);
    if (targetCard) {
        targetCard.classList.remove('hidden');
        console.log('Showing question', index);
    } else {
        console.error('Question card not found for index:', index);
    }
    
    // Update nav buttons
    document.querySelectorAll('.question-nav-btn').forEach(btn => {
        btn.classList.remove('current');
    });
    document.getElementById(`nav-${index}`).classList.add('current');
    
    currentQuestion = index;
    window.scrollTo(0, 0);
}

function selectOption(questionIndex, questionId, optionId) {
    // Update visual selection
    const container = document.getElementById(`options-${questionIndex}`);
    container.querySelectorAll('.option-card').forEach(card => {
        card.classList.remove('selected');
        card.querySelector('.option-dot').classList.add('hidden');
        card.querySelector('.option-circle').classList.remove('border-purple-600');
        card.querySelector('.option-circle').classList.add('border-gray-300');
    });
    
    const selectedCard = container.querySelector(`[data-option-id="${optionId}"]`);
    selectedCard.classList.add('selected');
    selectedCard.querySelector('.option-dot').classList.remove('hidden');
    selectedCard.querySelector('.option-circle').classList.remove('border-gray-300');
    selectedCard.querySelector('.option-circle').classList.add('border-purple-600');
    
    // Update nav button
    const navBtn = document.getElementById(`nav-${questionIndex}`);
    navBtn.classList.remove('unanswered');
    navBtn.classList.add('answered');
    
    // Save answer via AJAX
    fetch('{{ route("student.exams.save_answer", ["exam" => $exam, "attempt" => $attempt]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            question_id: questionId,
            option_id: optionId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Answer saved for question', questionId);
        }
    })
    .catch(error => {
        console.error('Error saving answer:', error);
    });
    
    // Update answered count
    savedAnswers[questionId] = optionId;
    updateAnsweredCount();
    
    // Show saved indicator
    const saveIndicator = document.createElement('span');
    saveIndicator.className = 'save-indicator text-green-600 text-xs absolute -top-1 -right-1';
    saveIndicator.textContent = '✓';
    if (!navBtn.querySelector('.save-indicator')) {
        navBtn.style.position = 'relative';
        navBtn.appendChild(saveIndicator);
        setTimeout(() => saveIndicator.remove(), 2000);
    }
}

function updateAnsweredCount() {
    const count = Object.keys(savedAnswers).length;
    document.getElementById('answered-count').textContent = count;
    document.getElementById('modal-answered').textContent = count;
}

function submitExam() {
    console.log('submitExam called');
    const modal = document.getElementById('submit-modal');
    console.log('Modal element:', modal);
    if (modal) {
        modal.classList.remove('hidden');
        console.log('Modal shown');
    } else {
        console.error('Modal not found!');
    }
}

function closeSubmitModal() {
    console.log('closeSubmitModal called');
    const modal = document.getElementById('submit-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Timer countdown
@if($timeRemaining !== null)
let timeRemaining = {{ (int)$timeRemaining }};
function updateTimer() {
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    document.getElementById('timer').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    
    if (timeRemaining <= 0) {
        // Auto submit when time expires
        document.querySelector('form').submit();
    } else {
        timeRemaining--;
        setTimeout(updateTimer, 1000);
    }
}
updateTimer();
@endif

// Initialize
updateAnsweredCount();

// Handle form submission
document.addEventListener('DOMContentLoaded', function() {
    const submitForm = document.getElementById('submit-form');
    if (submitForm) {
        submitForm.addEventListener('submit', function(e) {
            console.log('Form is being submitted!');
        });
    }
});

// Mark already saved answers on page load
function markSavedAnswers() {
    console.log('Marking saved answers:', savedAnswers);
    Object.entries(savedAnswers).forEach(([questionId, optionId]) => {
        // Find the question card with this questionId
        const card = document.querySelector(`.question-card[data-question-id="${questionId}"]`);
        if (card) {
            console.log('Found card for question', questionId);
            // Find option in this card
            const optionCard = card.querySelector(`[data-option-id="${optionId}"]`);
            if (optionCard) {
                console.log('Marking option', optionId, 'as selected');
                optionCard.classList.add('selected');
                optionCard.querySelector('.option-dot').classList.remove('hidden');
                optionCard.querySelector('.option-circle').classList.remove('border-gray-300');
                optionCard.querySelector('.option-circle').classList.add('border-purple-600');
            }
        } else {
            console.log('Card not found for question', questionId);
        }
    });
}

// Call on page load
document.addEventListener('DOMContentLoaded', markSavedAnswers);
</script>
@endpush
