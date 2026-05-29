@extends('layouts.tenant')

@section('title', 'Edit Notice')
@section('page-title', 'Edit Notice')

@section('page-content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-4">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.notices.update', $notice) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label for="title" class="form-label">Notice Title *</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $notice->title) }}" 
                           class="form-input" placeholder="Enter notice title" required>
                </div>

                <div>
                    <label for="content" class="form-label">Content *</label>
                    <textarea id="content" name="content" rows="5" 
                              class="form-input" placeholder="Enter notice content" required>{{ old('content', $notice->content) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="form-label">Type</label>
                        <select id="type" name="type" class="form-input">
                            <option value="general" {{ old('type', $notice->type) == 'general' ? 'selected' : '' }}>General</option>
                            <option value="important" {{ old('type', $notice->type) == 'important' ? 'selected' : '' }}>Important</option>
                            <option value="urgent" {{ old('type', $notice->type) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="event" {{ old('type', $notice->type) == 'event' ? 'selected' : '' }}>Event</option>
                        </select>
                    </div>

                    <div>
                        <label for="audience" class="form-label">Audience</label>
                        <select id="audience" name="audience" class="form-input">
                            <option value="all" {{ old('audience', $notice->audience) == 'all' ? 'selected' : '' }}>All</option>
                            <option value="students" {{ old('audience', $notice->audience) == 'students' ? 'selected' : '' }}>Students Only</option>
                            <option value="teachers" {{ old('audience', $notice->audience) == 'teachers' ? 'selected' : '' }}>Teachers Only</option>
                        </select>
                    </div>
                </div>

                @php
                    $courses = \App\Models\Course::where('tenant_id', Auth::user()->tenant_id)
                        ->where('status', 'active')
                        ->get();
                @endphp

                <div>
                    <label for="course_id" class="form-label">Specific Course (Optional)</label>
                    <select id="course_id" name="course_id" class="form-input">
                        <option value="">-- All Courses --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $notice->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_published" name="is_published" value="1" 
                           class="h-4 w-4 text-blue-600 rounded border-gray-300"
                           {{ old('is_published', $notice->is_published) ? 'checked' : '' }}>
                    <label for="is_published" class="ml-2 text-sm text-gray-600">
                        Published
                    </label>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="btn-primary">
                    Update Notice
                </button>
                <a href="{{ route('tenant.notices.index') }}" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
