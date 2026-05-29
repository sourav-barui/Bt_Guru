@extends('layouts.tenant')

@section('title', 'Edit Lesson')
@section('page-title', 'Edit Lesson')

@section('page-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-6">
            <a href="{{ route('tenant.curriculum.index', $course) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Curriculum
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.curriculum.lessons.update', [$course, $lesson]) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="title" class="form-label">Lesson Title *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $lesson->title) }}" 
                           class="form-input" placeholder="e.g., Variables and Constants" required>
                </div>

                <div>
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="3" 
                              class="form-input" placeholder="Brief description of this lesson">{{ old('description', $lesson->description) }}</textarea>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h4 class="font-medium text-gray-900 mb-3">Video Content (Optional)</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="video_type" class="form-label">Video Platform</label>
                            <select id="video_type" name="video_type" class="form-input">
                                <option value="">-- Select --</option>
                                <option value="youtube" {{ old('video_type', $lesson->video_type) == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="vimeo" {{ old('video_type', $lesson->video_type) == 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                                <option value="other" {{ old('video_type', $lesson->video_type) == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div>
                            <label for="video_url" class="form-label">Video URL</label>
                            <input type="url" id="video_url" name="video_url" value="{{ old('video_url', $lesson->video_url) }}" 
                                   class="form-input" placeholder="https://...">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="duration_minutes" class="form-label">Duration (minutes)</label>
                        <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes) }}" 
                               class="form-input w-32" placeholder="15" min="1">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-gray-200 pt-4">
                    <div>
                        <label for="order" class="form-label">Display Order</label>
                        <input type="number" id="order" name="order" value="{{ old('order', $lesson->order) }}" 
                               class="form-input" placeholder="0" min="0">
                    </div>

                    <div>
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-input">
                            <option value="active" {{ old('status', $lesson->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="draft" {{ old('status', $lesson->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="inactive" {{ old('status', $lesson->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="btn-primary">
                    Update Lesson
                </button>
                <a href="{{ route('tenant.curriculum.index', $course) }}" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
