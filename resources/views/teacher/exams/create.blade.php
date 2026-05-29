@extends('layouts.teacher')

@section('title', 'Create Exam - ' . $course->title)
@section('page-title', 'Create New Exam')

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('teacher.courses.show', $course) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Course
            </a>
            <p class="text-sm text-gray-500">Create MCQ exam for {{ $course->title }}</p>
        </div>
    </div>

    <!-- Level Info Card -->
    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm text-purple-600 font-medium">Exam Level</p>
                <p class="text-gray-900">
                    @if($level === 'subject' && $subject)
                        Subject: <span class="font-semibold">{{ $subject->title }}</span>
                    @elseif($level === 'chapter' && $chapter)
                        Chapter: <span class="font-semibold">{{ $chapter->title }}</span>
                        @if($subject) <span class="text-gray-500">(Subject: {{ $subject->title }})</span> @endif
                    @elseif($level === 'lesson' && $lesson)
                        Lesson: <span class="font-semibold">{{ $lesson->title }}</span>
                        @if($chapter) <span class="text-gray-500">(Chapter: {{ $chapter->title }})</span> @endif
                    @else
                        Course Level
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Exam Form -->
    <form action="{{ route('teacher.exams.store', $course) }}" method="POST" class="space-y-6">
        @csrf

        <!-- Hidden Level Info -->
        <input type="hidden" name="level" value="{{ $level ?? 'course' }}">
        @if($subject)
            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
        @endif
        @if($chapter)
            <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
        @endif
        @if($lesson)
            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
        @endif

        <!-- Basic Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Exam Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required
                           class="form-input w-full"
                           placeholder="e.g., Mid Term Examination, Chapter 1 Quiz"
                           value="{{ old('title') }}">
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="form-input w-full"
                              placeholder="Instructions or description for students...">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Exam Template <span class="text-red-500">*</span>
                    </label>
                    <select name="template" required class="form-input w-full">
                        <option value="default" selected>Default Template</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Exam Settings -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" min="1"
                           class="form-input w-full" placeholder="e.g., 60"
                           value="{{ old('duration_minutes') }}">
                    <p class="text-xs text-gray-500 mt-1">Leave empty for no time limit</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Passing Percentage</label>
                    <input type="number" name="passing_percentage" min="0" max="100"
                           class="form-input w-full" placeholder="e.g., 40"
                           value="{{ old('passing_percentage', 40) }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Attempts</label>
                    <input type="number" name="max_attempts" min="1"
                           class="form-input w-full" placeholder="e.g., 3"
                           value="{{ old('max_attempts') }}">
                    <p class="text-xs text-gray-500 mt-1">Leave empty for unlimited</p>
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Shuffle questions randomly</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="show_result_immediately" value="1" checked
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Show result immediately after submission</span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="allow_multiple_attempts" value="1" {{ old('allow_multiple_attempts') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">Allow multiple attempts</span>
                </label>
            </div>
        </div>

        <!-- Scheduling -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Scheduling (Optional)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                    <input type="datetime-local" name="start_time" class="form-input w-full" value="{{ old('start_time') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                    <input type="datetime-local" name="end_time" class="form-input w-full" value="{{ old('end_time') }}">
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-2">Leave empty to make exam available immediately and indefinitely</p>
        </div>

        <!-- Submit -->
        <div class="flex items-center gap-3">
            <button type="submit" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Exam & Add Questions
            </button>
            <a href="{{ route('teacher.courses.show', $course) }}" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
