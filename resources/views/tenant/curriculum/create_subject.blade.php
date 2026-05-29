@extends('layouts.tenant')

@section('title', 'Create Subject')
@section('page-title', 'Create Subject')

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

        <form method="POST" action="{{ route('tenant.curriculum.subjects.store', [$course, $curriculum]) }}">
            @csrf

            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-700">
                    <span class="font-medium">Curriculum:</span> {{ $curriculum->title }}
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="title" class="form-label">Subject Title *</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" 
                           class="form-input" placeholder="e.g., Mathematics Fundamentals" required>
                </div>

                <div>
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" rows="3" 
                              class="form-input" placeholder="Brief description of this subject">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="order" class="form-label">Display Order</label>
                        <input type="number" id="order" name="order" value="{{ old('order', 0) }}" 
                               class="form-input" placeholder="0" min="0">
                    </div>

                    <div>
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-input">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="btn-primary">
                    Create Subject
                </button>
                <a href="{{ route('tenant.curriculum.index', $course) }}" class="btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
