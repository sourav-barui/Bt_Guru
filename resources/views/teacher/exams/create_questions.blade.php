@extends('layouts.teacher')

@section('title', 'Add Questions - ' . $exam->title)
@section('page-title', 'Add Questions to Exam')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('teacher.exams.show', [$course, $exam]) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Exam
            </a>
            <h1 class="text-xl font-semibold text-gray-900">{{ $exam->title }}</h1>
            <p class="text-sm text-gray-500">Level: {{ $exam->level_name }}</p>
        </div>
        <div class="flex gap-2">
            <button type="button" onclick="addSection()" class="btn-secondary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                </svg>
                Add Section
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-800">{{ session('error') }}</div>
    @endif

    <!-- Section Form Modal -->
    <div id="section-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-4">Add New Section</h3>
            <form action="{{ route('teacher.exams.sections.store', [$course, $exam]) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section Title</label>
                        <input type="text" name="title" required class="form-input w-full" placeholder="e.g., Mathematics, General Knowledge">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="form-input w-full" placeholder="Optional description..."></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marks per Question</label>
                            <input type="number" name="marks_per_question" value="1" min="1" class="form-input w-full">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Negative Marks</label>
                            <input type="number" name="negative_marks_per_question" value="0" min="0" step="0.25" class="form-input w-full">
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex gap-2">
                    <button type="submit" class="btn-primary">Add Section</button>
                    <button type="button" onclick="closeSectionModal()" class="btn-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- CSV Import Forms -->
    @foreach($exam->sections as $section)
    <form action="{{ route('teacher.exams.questions.import', [$course, $exam]) }}" method="POST" enctype="multipart/form-data" id="csv-form-section-{{ $section->id }}" class="hidden">
        @csrf
        <input type="hidden" name="section_id" value="{{ $section->id }}">
    </form>
    @endforeach

    <form action="{{ route('teacher.exams.questions.import', [$course, $exam]) }}" method="POST" enctype="multipart/form-data" id="csv-form-no-section" class="hidden">
        @csrf
    </form>

    <!-- Questions Form -->
    <form action="{{ route('teacher.exams.questions.store', [$course, $exam]) }}" method="POST" id="questions-form">
        @csrf

        @foreach($exam->sections as $section)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $section->title }}</h3>
                    @if($section->description)<p class="text-sm text-gray-500">{{ $section->description }}</p>@endif
                    <p class="text-xs text-purple-600 mt-1">+{{ $section->marks_per_question }} marks per question@if($section->negative_marks_per_question > 0), -{{ $section->negative_marks_per_question }} for wrong answer@endif</p>
                </div>
                <span class="text-sm text-gray-400">Section {{ $loop->iteration }}</span>
            </div>

            <div class="space-y-4" id="section-{{ $section->id }}-questions"></div>

            <div class="mt-4 flex items-center gap-4">
                <button type="button" onclick="addQuestion({{ $section->id }})" class="inline-flex items-center gap-2 text-sm text-purple-600 hover:text-purple-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Question
                </button>
                <span class="text-gray-300">|</span>
                <button type="button" onclick="toggleSectionImport({{ $section->id }})" class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Bulk Import CSV
                </button>
            </div>

            <div id="section-import-{{ $section->id }}" class="hidden mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start justify-between mb-3">
                    <h4 class="font-medium text-gray-900">Import Questions to {{ $section->title }}</h4>
                    <a href="{{ route('teacher.exams.questions.template', [$course, $exam]) }}" class="text-sm text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Download Template
                    </a>
                </div>
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div><label class="block text-xs font-medium text-gray-700 mb-1">CSV File</label><input type="file" id="csv-file-section-{{ $section->id }}" form="csv-form-section-{{ $section->id }}" name="csv_file" accept=".csv,.txt" required class="form-input w-full text-sm py-1.5"></div>
                        <div><label class="block text-xs font-medium text-gray-700 mb-1">Marks (per Q)</label><input type="number" form="csv-form-section-{{ $section->id }}" name="default_marks" value="{{ $section->marks_per_question }}" min="1" class="form-input w-full text-sm py-1.5"></div>
                        <div><label class="block text-xs font-medium text-gray-700 mb-1">Negative Marks</label><input type="number" form="csv-form-section-{{ $section->id }}" name="default_negative_marks" value="{{ $section->negative_marks_per_question }}" min="0" step="0.25" class="form-input w-full text-sm py-1.5"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" form="csv-form-section-{{ $section->id }}" class="btn-primary text-sm py-1.5 px-3 inline-flex items-center gap-1.5">Import</button>
                        <button type="button" onclick="toggleSectionImport({{ $section->id }})" class="btn-secondary text-sm py-1.5 px-3">Cancel</button>
                    </div>
                </div>
                <div class="mt-3 p-2 bg-white rounded border border-blue-100 text-xs"><p class="font-medium text-gray-700">CSV Format:</p><code class="block text-gray-600 mt-1">question,option1,option2,option3,option4,correct_option</code></div>
            </div>
        </div>
        @endforeach

        @if($exam->sections->count() === 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Questions</h3>
            <div id="questions-container" class="space-y-6"></div>
            <div class="mt-6 flex items-center gap-4">
                <button type="button" onclick="addQuestion(null)" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Question
                </button>
                <span class="text-gray-300">|</span>
                <button type="button" onclick="toggleNoSectionImport()" class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Bulk Import CSV
                </button>
            </div>
            <div id="no-section-import" class="hidden mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <div class="flex items-start justify-between mb-3">
                    <h4 class="font-medium text-gray-900">Import Questions via CSV</h4>
                    <a href="{{ route('teacher.exams.questions.template', [$course, $exam]) }}" class="text-sm text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Download Template
                    </a>
                </div>
                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div><label class="block text-xs font-medium text-gray-700 mb-1">CSV File</label><input type="file" form="csv-form-no-section" name="csv_file" accept=".csv,.txt" required class="form-input w-full text-sm py-1.5"></div>
                        <div><label class="block text-xs font-medium text-gray-700 mb-1">Marks (per Q)</label><input type="number" form="csv-form-no-section" name="default_marks" value="1" min="1" class="form-input w-full text-sm py-1.5"></div>
                        <div><label class="block text-xs font-medium text-gray-700 mb-1">Negative Marks</label><input type="number" form="csv-form-no-section" name="default_negative_marks" value="0" min="0" step="0.25" class="form-input w-full text-sm py-1.5"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="submit" form="csv-form-no-section" class="btn-primary text-sm py-1.5 px-3 inline-flex items-center gap-1.5">Import</button>
                        <button type="button" onclick="toggleNoSectionImport()" class="btn-secondary text-sm py-1.5 px-3">Cancel</button>
                    </div>
                </div>
                <div class="mt-3 p-2 bg-white rounded border border-blue-100 text-xs"><p class="font-medium text-gray-700">CSV Format:</p><code class="block text-gray-600 mt-1">question,option1,option2,option3,option4,correct_option</code></div>
            </div>
        </div>
        @endif

        <!-- Question Template -->
        <template id="question-template">
            <div class="question-card border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-500">Question #<span class="q-number"></span></span>
                    <button type="button" onclick="removeQuestion(this)" class="text-red-500 hover:text-red-700 text-sm">Remove</button>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                        <textarea name="questions[INDEX][question_text]" required rows="2" class="form-input w-full" placeholder="Enter your question..."></textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select name="questions[INDEX][question_type]" class="form-input w-full q-type" onchange="updateOptions(this)">
                                <option value="single_choice">Single Choice</option>
                                <option value="true_false">True/False</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marks</label>
                            <input type="number" name="questions[INDEX][marks]" value="1" min="1" class="form-input w-full">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Negative Marks</label>
                            <input type="number" name="questions[INDEX][negative_marks]" value="0" min="0" step="0.25" class="form-input w-full">
                        </div>
                    </div>
                    <input type="hidden" name="questions[INDEX][section_id]" class="section-input" value="">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Explanation (Optional)</label>
                        <textarea name="questions[INDEX][explanation]" rows="2" class="form-input w-full" placeholder="Explanation for correct answer..."></textarea>
                    </div>
                    <div class="options-container space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Options</label>
                        <div class="options-list space-y-2"></div>
                        <button type="button" onclick="addOption(this)" class="text-sm text-purple-600 hover:text-purple-700 inline-flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add Option
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <template id="option-template">
            <div class="option-item flex items-center gap-2">
                <input type="radio" name="questions[INDEX][correct_option]" value="OPTION_INDEX" class="correct-option">
                <input type="text" name="questions[INDEX][options][OPTION_INDEX][option_text]" required class="form-input flex-1 text-sm" placeholder="Option text">
                <label class="flex items-center gap-1 text-sm cursor-pointer">
                    <input type="checkbox" name="questions[INDEX][options][OPTION_INDEX][is_correct]" value="1" class="is-correct rounded">
                    <span class="text-gray-600">Correct</span>
                </label>
                <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </template>

        <div class="flex items-center gap-3 pt-6 border-t">
            <button type="submit" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Save Questions
            </button>
            <a href="{{ route('teacher.exams.show', [$course, $exam]) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let questionCounter = 0;
function addSection() { document.getElementById('section-modal').classList.remove('hidden'); }
function closeSectionModal() { document.getElementById('section-modal').classList.add('hidden'); }
function toggleSectionImport(sectionId) { document.getElementById('section-import-' + sectionId).classList.toggle('hidden'); }
function toggleNoSectionImport() { document.getElementById('no-section-import').classList.toggle('hidden'); }

function addQuestion(sectionId) {
    questionCounter++;
    const template = document.getElementById('question-template');
    const clone = template.content.cloneNode(true);
    const index = questionCounter;
    clone.querySelector('.q-number').textContent = index;
    clone.querySelectorAll('[name*="[INDEX]"]').forEach(el => { el.name = el.name.replace('INDEX', index); });
    if (sectionId) { clone.querySelector('.section-input').value = sectionId; }
    const optionsList = clone.querySelector('.options-list');
    ['True', 'False'].forEach((opt, i) => {
        const optTemplate = document.getElementById('option-template');
        const optClone = optTemplate.content.cloneNode(true);
        optClone.querySelectorAll('[name*="[INDEX]"]').forEach(el => { el.name = el.name.replace('INDEX', index); });
        optClone.querySelectorAll('[name*="[OPTION_INDEX]"]').forEach(el => { el.name = el.name.replace('OPTION_INDEX', i); });
        optClone.querySelector('.correct-option').value = i;
        optClone.querySelector('input[type="text"]').value = opt;
        if (opt === 'True') { optClone.querySelector('.is-correct').checked = true; }
        optionsList.appendChild(optClone);
    });
    let container = sectionId ? document.getElementById(`section-${sectionId}-questions`) : document.getElementById('questions-container');
    container.appendChild(clone);
}

function removeQuestion(btn) { btn.closest('.question-card').remove(); }

function addOption(btn) {
    const optionsList = btn.previousElementSibling;
    const questionCard = btn.closest('.question-card');
    const index = questionCard.querySelector('[name*="[question_text]"]').name.match(/\[(\d+)\]/)[1];
    const optionCount = optionsList.children.length;
    const optTemplate = document.getElementById('option-template');
    const optClone = optTemplate.content.cloneNode(true);
    optClone.querySelectorAll('[name*="[INDEX]"]').forEach(el => { el.name = el.name.replace('INDEX', index); });
    optClone.querySelectorAll('[name*="[OPTION_INDEX]"]').forEach(el => { el.name = el.name.replace('OPTION_INDEX', optionCount); });
    optClone.querySelector('.correct-option').value = optionCount;
    optionsList.appendChild(optClone);
}

function removeOption(btn) {
    const optionItem = btn.closest('.option-item');
    const optionsList = optionItem.parentElement;
    if (optionsList.children.length <= 2) { alert('Minimum 2 options required'); return; }
    optionItem.remove();
    Array.from(optionsList.children).forEach((opt, i) => {
        opt.querySelector('.correct-option').value = i;
        opt.querySelectorAll('[name*="[options]"]').forEach(el => { el.name = el.name.replace(/\[options\]\[\d+\]/, `[options][${i}]`); });
    });
}

function updateOptions(select) {
    const questionCard = select.closest('.question-card');
    const optionsList = questionCard.querySelector('.options-list');
    const index = questionCard.querySelector('[name*="[question_text]"]').name.match(/\[(\d+)\]/)[1];
    if (select.value === 'true_false') {
        optionsList.innerHTML = '';
        ['True', 'False'].forEach((opt, i) => {
            const optTemplate = document.getElementById('option-template');
            const optClone = optTemplate.content.cloneNode(true);
            optClone.querySelectorAll('[name*="[INDEX]"]').forEach(el => { el.name = el.name.replace('INDEX', index); });
            optClone.querySelectorAll('[name*="[OPTION_INDEX]"]').forEach(el => { el.name = el.name.replace('OPTION_INDEX', i); });
            optClone.querySelector('.correct-option').value = i;
            optClone.querySelector('input[type="text"]').value = opt;
            optClone.querySelector('input[type="text"]').readOnly = true;
            if (opt === 'True') { optClone.querySelector('.is-correct').checked = true; }
            optionsList.appendChild(optClone);
        });
    } else {
        optionsList.innerHTML = '';
        for (let i = 0; i < 2; i++) {
            const optTemplate = document.getElementById('option-template');
            const optClone = optTemplate.content.cloneNode(true);
            optClone.querySelectorAll('[name*="[INDEX]"]').forEach(el => { el.name = el.name.replace('INDEX', index); });
            optClone.querySelectorAll('[name*="[OPTION_INDEX]"]').forEach(el => { el.name = el.name.replace('OPTION_INDEX', i); });
            optClone.querySelector('.correct-option').value = i;
            optionsList.appendChild(optClone);
        }
    }
}

document.getElementById('section-modal').addEventListener('click', function(e) { if (e.target === this) closeSectionModal(); });
</script>
@endpush
@endsection
