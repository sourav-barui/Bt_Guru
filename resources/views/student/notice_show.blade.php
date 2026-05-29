@extends('layouts.student_mobile')

@section('title', $notice->title)

@section('mobile-content')
<!-- Notice Card -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <!-- Header -->
    <div class="px-4 py-4 bg-gradient-to-r from-blue-500 to-indigo-600 text-white">
        <div class="flex items-center gap-2 mb-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
            </svg>
            <span class="text-sm font-medium">Notice</span>
        </div>
        <h1 class="text-xl font-bold">{{ $notice->title }}</h1>
    </div>

    <!-- Meta Info -->
    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            {{ $notice->created_at->format('d M Y, h:i A') }}
        </div>
        @if($notice->is_pinned)
            <span class="px-2 py-1 bg-red-100 text-red-600 text-xs font-medium rounded-full">Pinned</span>
        @endif
    </div>

    <!-- Content -->
    <div class="p-4">
        @if($notice->image)
            <img src="{{ asset('storage/' . $notice->image) }}" alt="Notice Image" class="w-full rounded-xl mb-4">
        @endif

        <div class="prose prose-sm max-w-none text-gray-700">
            {!! nl2br(e($notice->content)) !!}
        </div>
    </div>

    <!-- Footer -->
    <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
        <a href="{{ url()->previous() == url()->current() ? route('student.dashboard') : url()->previous() }}" 
           class="inline-flex items-center gap-2 text-blue-600 font-medium hover:text-blue-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back
        </a>
    </div>
</div>
@endsection
