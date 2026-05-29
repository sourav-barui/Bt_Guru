@extends('layouts.tenant')
@section('title', 'Live Classes — ' . $course->title)
@section('page-title', 'Live Classes')

@section('page-content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('tenant.curriculum.index', $course) }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                {{ $course->title }}
            </a>
            <p class="text-sm text-gray-500">Schedule and manage live classes for students</p>
        </div>
        <a href="{{ route('tenant.live_classes.create', $course) }}" class="btn-primary inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Schedule Live Class
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 font-medium text-sm">{{ session('success') }}</div>
    @endif

    @php
        $upcoming  = $liveClasses->where('status', 'scheduled')->where('scheduled_at', '>', now())->sortBy('scheduled_at');
        $liveNow   = $liveClasses->where('status', 'live');
        $completed = $liveClasses->whereIn('status', ['completed', 'cancelled'])->sortByDesc('scheduled_at');
    @endphp

    {{-- Live Right Now --}}
    @if($liveNow->count())
    <div class="bg-red-50 border-2 border-red-300 rounded-2xl p-5 space-y-3">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 text-sm font-extrabold text-red-700">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse inline-block"></span>
                LIVE RIGHT NOW
            </span>
        </div>
        @foreach($liveNow as $lc)
        @include('tenant.live_classes._card', ['lc' => $lc, 'course' => $course])
        @endforeach
    </div>
    @endif

    {{-- Upcoming --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Upcoming Classes</h3>
            <span class="text-sm text-gray-400">{{ $upcoming->count() }} scheduled</span>
        </div>
        @if($upcoming->count())
        <div class="divide-y divide-gray-50">
            @foreach($upcoming as $lc)
            @include('tenant.live_classes._card', ['lc' => $lc, 'course' => $course])
            @endforeach
        </div>
        @else
        <div class="p-10 text-center text-gray-400 text-sm">No upcoming classes. Schedule one!</div>
        @endif
    </div>

    {{-- Past --}}
    @if($completed->count())
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-700">Past Classes</h3>
        </div>
        <div class="divide-y divide-gray-50">
            @foreach($completed as $lc)
            @include('tenant.live_classes._card', ['lc' => $lc, 'course' => $course])
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
