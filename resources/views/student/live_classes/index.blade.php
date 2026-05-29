@extends('layouts.student_mobile')
@section('title', 'Live Classes')

@section('mobile-content')
<!-- Header with Gradient -->
<div class="tb-header-gradient" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 50%, #7f1d1d 100%);">
    <div class="flex items-center justify-between mb-4">
        <div>
            <p class="text-sm text-white/80">Online Sessions</p>
            <h1 class="text-2xl font-bold text-white">Live Classes</h1>
        </div>
        <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
        </div>
    </div>
</div>

<!-- Quick Stats Row -->
@php
    $liveCount = $liveNow->count();
    $upcomingCount = $upcoming->count();
    $pastCount = $past->count();
@endphp
<div class="tb-stats-row -mt-6 mb-4">
    <div class="tb-stat-card shadow-lg {{ $liveCount > 0 ? 'ring-2 ring-red-400' : '' }}">
        <p class="tb-stat-number {{ $liveCount > 0 ? 'text-red-600 animate-pulse' : 'text-gray-500' }}">{{ $liveCount }}</p>
        <p class="tb-stat-label">Live Now</p>
    </div>
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number text-indigo-600">{{ $upcomingCount }}</p>
        <p class="tb-stat-label">Upcoming</p>
    </div>
    <div class="tb-stat-card shadow-lg">
        <p class="tb-stat-number text-gray-600">{{ $pastCount }}</p>
        <p class="tb-stat-label">Completed</p>
    </div>
</div>

<!-- Live Now Section -->
@if($liveCount > 0)
<div class="px-4 mb-6">
    <div class="flex items-center gap-2 mb-3">
        <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
        <h2 class="tb-section-title" style="margin: 0; color: #dc2626;">Live Right Now</h2>
    </div>
    @foreach($liveNow as $lc)
        @include('student.live_classes._card_new', ['lc' => $lc, 'mode' => 'live'])
    @endforeach
</div>
@endif

<!-- Upcoming Section -->
<div class="px-4 mb-6">
    <div class="flex items-center justify-between mb-3">
        <h2 class="tb-section-title" style="margin: 0;">Upcoming Classes</h2>
        @if($upcomingCount > 0)
            <span class="text-xs text-indigo-600 font-semibold">{{ $upcomingCount }} scheduled</span>
        @endif
    </div>
    @forelse($upcoming as $lc)
        @include('student.live_classes._card_new', ['lc' => $lc, 'mode' => 'upcoming'])
    @empty
        <div class="tb-card text-center py-8">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <p class="text-gray-500 mb-2">No upcoming classes</p>
            <p class="text-xs text-gray-400">Check back later for new sessions</p>
        </div>
    @endforelse
</div>

<!-- Past Section -->
@if($pastCount > 0)
<div class="px-4 pb-6">
    <h2 class="tb-section-title" style="margin: 0 0 12px 0;">Past Classes</h2>
    @foreach($past as $lc)
        @include('student.live_classes._card_new', ['lc' => $lc, 'mode' => 'past'])
    @endforeach
</div>
@endif

@endsection
