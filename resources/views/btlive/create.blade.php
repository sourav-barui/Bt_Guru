@extends('layouts.tenant')

@section('title', 'New BTLive — ' . $course->title)
@section('page-title', 'Schedule BTLive Class')

@section('page-content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('tenant.live_classes.index', $course) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Live Classes
        </a>
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">New BTLive Class</h1>
                <p class="text-gray-500">{{ $course->title }}</p>
            </div>
        </div>
    </div>
    
    {{-- Info Card --}}
    <div class="bg-gradient-to-r from-red-50 to-orange-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-sm font-medium text-red-900">Native BTLive Classroom</p>
                <p class="text-sm text-red-700 mt-1">
                    Students join directly in the app with embedded video. No external links needed. 
                    Supports up to 5000 concurrent students with teacher-controlled audio/video.
                </p>
            </div>
        </div>
    </div>

    {{-- Form --}}
    <form action="{{ route('btlive.store', $course) }}" method="POST" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
        @csrf
        
        {{-- Title --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Class Title <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                   placeholder="e.g., Mathematics - Algebra Basics">
            @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        
        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3" 
                      class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                      placeholder="What will be covered in this class?">{{ old('description') }}</textarea>
        </div>
        
        {{-- Subject --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
            <select name="subject_id" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                <option value="">-- Select Subject (Optional) --</option>
                @foreach($subjects as $id => $name)
                    <option value="{{ $id }}" {{ old('subject_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        
        {{-- Date & Time --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date & Time <span class="text-red-500">*</span></label>
                <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required
                       class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                @error('scheduled_at')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Duration <span class="text-red-500">*</span></label>
                <select name="duration_minutes" required class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    <option value="30" {{ old('duration_minutes') == 30 ? 'selected' : '' }}>30 minutes</option>
                    <option value="45" {{ old('duration_minutes') == 45 ? 'selected' : '' }}>45 minutes</option>
                    <option value="60" {{ old('duration_minutes') == 60 ? 'selected' : '' }} selected>1 hour</option>
                    <option value="90" {{ old('duration_minutes') == 90 ? 'selected' : '' }}>1.5 hours</option>
                    <option value="120" {{ old('duration_minutes') == 120 ? 'selected' : '' }}>2 hours</option>
                </select>
            </div>
        </div>
        
        {{-- BTLive Settings --}}
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                BTLive Settings
            </h3>
            
            <div class="space-y-3">
                {{-- Public Class --}}
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                    <input type="checkbox" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}
                           class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Public Class</p>
                        <p class="text-xs text-gray-500">All students in your institute can join</p>
                    </div>
                </label>
                
                {{-- Lobby --}}
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                    <input type="checkbox" name="btlive_lobby_enabled" value="1" checked
                           class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Enable Lobby</p>
                        <p class="text-xs text-gray-500">Students wait in lobby until teacher admits them</p>
                    </div>
                </label>
                
                {{-- Chat --}}
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                    <input type="checkbox" name="btlive_chat_enabled" value="1" checked
                           class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Enable Chat</p>
                        <p class="text-xs text-gray-500">Students can send messages during class</p>
                    </div>
                </label>
                
                {{-- Teacher Only Video --}}
                <label class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                    <input type="checkbox" name="btlive_teacher_only_video" value="1" checked
                           class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Teacher Video Only</p>
                        <p class="text-xs text-gray-500">Only teacher can share video (students watch)</p>
                    </div>
                </label>
            </div>
        </div>
        
        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('tenant.live_classes.index', $course) }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary bg-red-600 hover:bg-red-700 inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Schedule BTLive
            </button>
        </div>
    </form>
</div>
@endsection
