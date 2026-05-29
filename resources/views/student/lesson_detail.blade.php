@extends('layouts.student_mobile')

@section('title', $lesson->title)

@push('styles')
<style>
    /* Vibrant Colors */
    .tb-divider { height: 1px; background: linear-gradient(90deg, #e5e7eb, transparent); margin: 12px 0; }
    
    /* Animated Header */
    .tb-course-header { background: linear-gradient(135deg, #10b981 0%, #059669 50%, #34d399 100%); color: white; padding: 24px 16px; margin: 0; border-radius: 0; position: relative; overflow: hidden; }
    .tb-course-header::before { content: ''; position: absolute; top: -50%; right: -20%; width: 200px; height: 200px; background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%); animation: pulse 3s ease-in-out infinite; }
    @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 0.5; } 50% { transform: scale(1.2); opacity: 0.8; } }
    
    /* Content Items */
    .tb-item { display: flex; align-items: center; gap: 12px; padding: 14px; background: white; border-radius: 14px; margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: all 0.2s; }
    .tb-item:hover { transform: translateX(4px); box-shadow: 0 4px 16px rgba(16,185,129,0.1); }
    .tb-item-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .tb-video-icon { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #2563eb; box-shadow: 0 4px 12px rgba(37,99,235,0.2); }
    .tb-note-icon { background: linear-gradient(135deg, #dcfce7, #bbf7d0); color: #16a34a; box-shadow: 0 4px 12px rgba(22,163,74,0.2); }
    .tb-item-content { flex: 1; }
    .tb-item-title { font-size: 15px; font-weight: 700; color: #1f2937; margin-bottom: 2px; }
    .tb-item-meta { font-size: 12px; color: #6b7280; }
    
    /* Glowing Buttons */
    .tb-btn-watch { background: linear-gradient(135deg, #2563eb, #1d4ed8); color: white; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 4px 12px rgba(37,99,235,0.3); transition: all 0.2s; }
    .tb-btn-watch:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(37,99,235,0.4); }
    .tb-btn-download { background: linear-gradient(135deg, #16a34a, #15803d); color: white; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 4px 12px rgba(22,163,74,0.3); transition: all 0.2s; }
    .tb-btn-download:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(22,163,74,0.4); }
    
    /* Section Label */
    .tb-section-label { font-size: 13px; font-weight: 800; color: #059669; text-transform: uppercase; letter-spacing: 1px; margin: 20px 0 12px; display: flex; align-items: center; gap: 8px; }
    .tb-section-label::before { content: ''; width: 4px; height: 16px; background: linear-gradient(180deg, #10b981, #34d399); border-radius: 2px; }

    /* Locked Content */
    .tb-item-locked { opacity: 0.55; position: relative; }
    .tb-item-locked .tb-item-icon { background: linear-gradient(135deg, #e5e7eb, #d1d5db) !important; color: #9ca3af !important; box-shadow: none !important; }
    .tb-lock-badge { background: #f3f4f6; color: #6b7280; padding: 6px 12px; border-radius: 10px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
    
    /* Live Class */
    .tb-live-icon { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; box-shadow: 0 4px 12px rgba(220,38,38,0.2); }
    .tb-btn-join { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 4px 12px rgba(220,38,38,0.3); transition: all 0.2s; }
    .tb-btn-join:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(220,38,38,0.4); }
    /* Exams */
    .tb-exam-icon { background: linear-gradient(135deg, #f3e8ff, #e9d5ff); color: #7c3aed; box-shadow: 0 4px 12px rgba(124,58,237,0.2); }
    .tb-btn-exam { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 4px 12px rgba(124,58,237,0.3); transition: all 0.2s; }
    .tb-exam-section-label { font-size: 13px; font-weight: 800; color: #7c3aed; text-transform: uppercase; letter-spacing: 1px; margin: 20px 0 12px; display: flex; align-items: center; gap: 8px; }
    .tb-exam-section-label::before { content: ''; width: 4px; height: 16px; background: linear-gradient(180deg, #7c3aed, #6d28d9); border-radius: 2px; }
    .tb-live-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; }
    .tb-live-badge.pulse::before { content: ''; width: 6px; height: 6px; background: #ef4444; border-radius: 50%; animation: pulse-dot 1.5s ease-in-out infinite; }
    @keyframes pulse-dot { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(0.8); } }
    .tb-live-section-label { font-size: 13px; font-weight: 800; color: #dc2626; text-transform: uppercase; letter-spacing: 1px; margin: 20px 0 12px; display: flex; align-items: center; gap: 8px; }
    .tb-live-section-label::before { content: ''; width: 4px; height: 16px; background: linear-gradient(180deg, #ef4444, #dc2626); border-radius: 2px; }
</style>
@endpush

@php
    use Carbon\Carbon;
    function isContentAccessible($date, $windows) {
        if (!$date || $windows->isEmpty()) return true;
        $d = Carbon::parse($date);
        foreach ($windows as $w) {
            if ($d->between($w->access_start, $w->access_end)) return true;
        }
        return false;
    }
    $isMonthly = $course->fees_type === 'monthly';
@endphp

@section('mobile-content')
<!-- Lesson Header -->
<div class="tb-course-header">
    <a href="{{ route('student.chapter.show', ['course' => $course, 'subject' => $subject, 'chapter' => $chapter]) }}" class="flex items-center gap-2 text-white/80 mb-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        <span class="text-sm">Back to {{ $chapter->title }}</span>
    </a>
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
        <div class="flex-1">
            <h1 class="text-xl font-bold">{{ $lesson->title }}</h1>
            <p class="text-white/70 text-sm">{{ $chapter->title }}</p>
        </div>
    </div>
</div>

<!-- Lesson Content -->
<div class="p-3">
    @if($lesson->description)
        <div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
            <p class="text-sm text-gray-600">{{ $lesson->description }}</p>
        </div>
    @endif

    <!-- Lesson Videos -->
    @if($lesson->contents->count() > 0)
        <div class="mb-4">
            <p class="tb-section-label">Lesson Videos</p>
            @foreach($lesson->contents as $content)
                @php $accessible = !$isMonthly || isContentAccessible($content->available_from, $accessWindows); @endphp
                <div class="tb-item {{ $accessible ? '' : 'tb-item-locked' }}">
                    <div class="tb-item-icon {{ $accessible ? 'tb-video-icon' : '' }}">
                        @if($accessible)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="tb-item-content">
                        <p class="tb-item-title">{{ $content->title }}</p>
                        @if($content->available_from)
                            <p class="tb-item-meta">📅 {{ $content->available_from->format('d M Y') }}</p>
                        @else
                            <p class="tb-item-meta">@if($content->user){{ $content->user->name }} • @endif{{ $content->created_at->diffForHumans() }}</p>
                        @endif
                        @if($accessible && $content->video_type)
                            <span class="inline-block mt-1 text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">{{ ucfirst($content->video_type) }}</span>
                        @endif
                    </div>
                    @if($accessible && $content->video_url)
                        <a href="{{ $content->video_url }}" target="_blank" class="tb-btn-watch">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            </svg>
                            Watch
                        </a>
                    @elseif(!$accessible)
                        <span class="tb-lock-badge">🔒 Locked</span>
                    @endif
                </div>
                <hr class="tb-divider">
            @endforeach
        </div>
    @endif

    <!-- Lesson Notes -->
    @if($lesson->notes->count() > 0)
        <div class="mb-4">
            <p class="tb-section-label">Lesson Notes</p>
            @foreach($lesson->notes as $note)
                @php $accessible = !$isMonthly || isContentAccessible($note->available_from, $accessWindows); @endphp
                <div class="tb-item {{ $accessible ? '' : 'tb-item-locked' }}">
                    <div class="tb-item-icon {{ $accessible ? 'tb-note-icon' : '' }}">
                        @if($accessible)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="tb-item-content">
                        <p class="tb-item-title">{{ $note->title }}</p>
                        @if($note->available_from)
                            <p class="tb-item-meta">📅 {{ $note->available_from->format('d M Y') }}</p>
                        @else
                            <p class="tb-item-meta">@if($note->user){{ $note->user->name }} • @endif{{ $note->created_at->diffForHumans() }}</p>
                        @endif
                    </div>
                    @if($accessible && $note->file_url)
                        <a href="{{ route('student.notes.show', $note) }}" class="tb-btn-download" title="{{ $note->is_downloadable ? 'View & Download PDF' : 'View PDF (Download not allowed)' }}">
                            @if($note->is_downloadable)
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3"></path>
                                </svg>
                                PDF
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View
                            @endif
                        </a>
                    @elseif(!$accessible)
                        <span class="tb-lock-badge">🔒 Locked</span>
                    @endif
                </div>
                <hr class="tb-divider">
            @endforeach
        </div>
    @endif

    <!-- Lesson Live Classes -->
    <!-- Lesson Level Exams -->
    @php
        $lessonExams = $course->exams->where('lesson_id', $lesson->id);
    @endphp
    @if($lessonExams->count() > 0)
        <div class="mt-4">
            <p class="tb-exam-section-label">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Lesson Exams
            </p>
            @foreach($lessonExams as $exam)
                <a href="{{ route('student.exams.show', $exam) }}" class="block tb-item">
                    <div class="tb-item-icon tb-exam-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div class="tb-item-content">
                        <p class="tb-item-title">{{ $exam->title }}</p>
                        <p class="tb-item-meta">{{ $exam->total_questions }} questions &bull; {{ $exam->total_marks }} marks{{ $exam->duration_minutes ? ' &bull; ' . $exam->duration_minutes . ' min' : '' }}</p>
                    </div>
                    <span class="tb-btn-exam">
                        Start
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </span>
                </a>
                <hr class="tb-divider">
            @endforeach
        </div>
    @endif

    <!-- Lesson Live Classes -->
    @if($lesson->liveClasses->count() > 0)
        <div class="mt-4">
            <p class="tb-live-section-label">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Lesson Live Classes
            </p>
            @foreach($lesson->liveClasses as $lc)
                <div class="tb-item">
                    <div class="tb-item-icon tb-live-icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="tb-item-content">
                        <p class="tb-item-title">{{ $lc->title }}</p>
                        <p class="tb-item-meta">{{ $lc->scheduled_at->format('d M Y, h:i A') }} • {{ $lc->duration_minutes }} min • {{ $lc->platform_label }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($lc->status === 'live')
                            <span class="tb-live-badge pulse">LIVE</span>
                        @elseif($lc->status === 'scheduled')
                            <span class="tb-live-badge">Upcoming</span>
                        @else
                            <span class="tb-live-badge">{{ ucfirst($lc->status) }}</span>
                        @endif
                        @if($lc->meeting_url && in_array($lc->status, ['live', 'scheduled']))
                            <a href="{{ $lc->secure_meeting_url }}" target="_blank" class="tb-btn-join">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                                Join
                            </a>
                        @endif
                    </div>
                </div>
                <hr class="tb-divider">
            @endforeach
        </div>
    @endif

    @if($lesson->contents->count() == 0 && $lesson->notes->count() == 0 && $lesson->liveClasses->count() == 0 && $lessonExams->count() == 0)
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p class="text-gray-500 text-lg">No content available for this lesson.</p>
        </div>
    @endif
</div>
@endsection
