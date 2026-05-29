@extends('layouts.student_mobile')

@section('title', $subject->title)

@push('styles')
<style>
    /* Vibrant Colors */
    .tb-divider { height: 1px; background: linear-gradient(90deg, #e5e7eb, transparent); margin: 12px 0; }
    .tb-level-divider { height: 3px; background: linear-gradient(90deg, #7c3aed, #ec4899, #f59e0b, #10b981, #3b82f6); margin: 20px 0; border-radius: 2px; }
    
    /* Animated Header */
    .tb-course-header { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 50%, #ec4899 100%); color: white; padding: 24px 16px; margin: 0; border-radius: 0; position: relative; overflow: hidden; }
    .tb-course-header::before { content: ''; position: absolute; top: -50%; right: -20%; width: 200px; height: 200px; background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%); animation: pulse 3s ease-in-out infinite; }
    @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 0.5; } 50% { transform: scale(1.2); opacity: 0.8; } }
    
    /* Content Items */
    .tb-item { display: flex; align-items: center; gap: 12px; padding: 14px; background: white; border-radius: 14px; margin-bottom: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); transition: all 0.2s; }
    .tb-item:hover { transform: translateX(4px); box-shadow: 0 4px 16px rgba(124,58,237,0.1); }
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
    .tb-section-label { font-size: 13px; font-weight: 800; color: #7c3aed; text-transform: uppercase; letter-spacing: 1px; margin: 20px 0 12px; display: flex; align-items: center; gap: 8px; }
    .tb-section-label::before { content: ''; width: 4px; height: 16px; background: linear-gradient(180deg, #7c3aed, #ec4899); border-radius: 2px; }
    
    /* Chapter Card */
    .tb-chapter-card { background: white; border-radius: 16px; padding: 18px; margin-bottom: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #f3f4f6; transition: all 0.3s; position: relative; overflow: hidden; }
    .tb-chapter-card:hover { transform: translateY(-3px); box-shadow: 0 12px 40px rgba(124,58,237,0.15); }
    .tb-chapter-card::after { content: ''; position: absolute; top: 0; right: 0; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(124,58,237,0.08), transparent); border-radius: 0 0 0 100%; }
    
    /* Badges */
    .tb-level-badge { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; border-radius: 12px; font-size: 14px; font-weight: 800; box-shadow: 0 4px 12px rgba(251,191,36,0.4); }
    .tb-stat-pill { display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; transition: all 0.2s; }
    .tb-stat-pill.blue { background: linear-gradient(135deg, #eff6ff, #dbeafe); color: #1d4ed8; }
    .tb-stat-pill.green { background: linear-gradient(135deg, #f0fdf4, #dcfce7); color: #15803d; }
    .tb-stat-pill.yellow { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; }
    .tb-stat-pill.red { background: linear-gradient(135deg, #fef2f2, #fee2e2); color: #dc2626; }
    
    /* Live Class */
    .tb-live-icon { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; box-shadow: 0 4px 12px rgba(220,38,38,0.2); }
    .tb-btn-join { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 4px 12px rgba(220,38,38,0.3); transition: all 0.2s; }
    .tb-exam-icon { background: linear-gradient(135deg, #f3e8ff, #e9d5ff); color: #7c3aed; box-shadow: 0 4px 12px rgba(124,58,237,0.2); }
    .tb-btn-exam { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; padding: 8px 16px; border-radius: 10px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 4px 12px rgba(124,58,237,0.3); transition: all 0.2s; }
    .tb-exam-section-label { font-size: 13px; font-weight: 800; color: #7c3aed; text-transform: uppercase; letter-spacing: 1px; margin: 20px 0 12px; display: flex; align-items: center; gap: 8px; }
    .tb-exam-section-label::before { content: ''; width: 4px; height: 16px; background: linear-gradient(180deg, #7c3aed, #6d28d9); border-radius: 2px; }
    .tb-btn-join:hover { transform: scale(1.05); box-shadow: 0 6px 20px rgba(220,38,38,0.4); }
    .tb-live-badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; }
    .tb-live-badge.pulse::before { content: ''; width: 6px; height: 6px; background: #ef4444; border-radius: 50%; animation: pulse-dot 1.5s ease-in-out infinite; }
    @keyframes pulse-dot { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(0.8); } }
    .tb-live-section-label { font-size: 13px; font-weight: 800; color: #dc2626; text-transform: uppercase; letter-spacing: 1px; margin: 20px 0 12px; display: flex; align-items: center; gap: 8px; }
    .tb-live-section-label::before { content: ''; width: 4px; height: 16px; background: linear-gradient(180deg, #ef4444, #dc2626); border-radius: 2px; }
</style>
@endpush

@php
// Helper function to check if content should be locked for monthly fee courses
function shouldLockContent($contentDate, $enrollment, $course) {
    // Only lock if course is monthly fee type
    if ($course->fees_type !== 'monthly') {
        return false;
    }

    // Get enrollment date (use enrolled_at if set, otherwise fallback to created_at)
    $enrollmentDate = $enrollment->enrolled_at ?? $enrollment->created_at;
    
    // Get content creation date
    $contentCreatedAt = $contentDate ? \Carbon\Carbon::parse($contentDate) : $enrollmentDate;

    // If content was created before enrollment, check if fee was paid for that month
    if ($contentCreatedAt->lessThan($enrollmentDate)) {
        $month = $contentCreatedAt->month;
        $year = $contentCreatedAt->year;

        // Check if monthly fee was paid for this month
        $monthlyFee = \App\Models\MonthlyFee::where('enrollment_id', $enrollment->id)
            ->where('month', $month)
            ->where('year', $year)
            ->where('status', 'paid')
            ->first();

        // Lock if fee not paid
        return !$monthlyFee;
    }

    return false; // Content created after enrollment - unlocked
}
@endphp

@section('mobile-content')
<!-- Subject Header -->
<div class="tb-course-header">
    <a href="{{ route('student.courses.access', $course) }}" class="flex items-center gap-2 text-white/80 mb-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        <span class="text-sm">Back to Subjects</span>
    </a>
    <div class="flex items-center gap-4">
        <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
        <div class="flex-1">
            <h1 class="text-xl font-bold">{{ $subject->title }}</h1>
            <p class="text-white/70 text-sm">{{ $course->title }}</p>
        </div>
    </div>
</div>

<!-- Subject Content -->
<div class="p-3">
    <!-- Subject Level Contents -->
    @if($subject->contents->count() > 0)
        <div class="mb-4">
            <p class="tb-section-label">Subject Videos</p>
            @foreach($subject->contents as $content)
                @php
                    $isLocked = shouldLockContent($content->created_at, $enrollment, $course);
                @endphp
                @if($isLocked)
                    {{-- Locked content --}}
                    <div class="tb-item opacity-50 bg-gray-50">
                        <div class="tb-item-icon bg-gray-200">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div class="tb-item-content">
                            <p class="tb-item-title text-gray-500">Locked Video</p>
                            <p class="tb-item-meta">Pay past month fee to unlock</p>
                        </div>
                        <a href="{{ route('student.payments.create', ['course_id' => $course->id, 'type' => 'past_month']) }}" class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded font-medium">
                            Pay to Unlock
                        </a>
                    </div>
                @else
                    <div class="tb-item">
                        <div class="tb-item-icon tb-video-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="tb-item-content">
                            <p class="tb-item-title">{{ $content->title }}</p>
                            <p class="tb-item-meta">@if($content->user){{ $content->user->name }} • @endif{{ $content->created_at->diffForHumans() }}</p>
                        </div>
                        @if($content->video_url)
                            <a href="{{ $content->video_url }}" target="_blank" class="tb-btn-watch">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 1 000-1.664z"></path>
                                </svg>
                                Watch
                            </a>
                        @endif
                    </div>
                @endif
                <hr class="tb-divider">
            @endforeach
        </div>
    @endif

    @if($subject->notes->count() > 0)
        <div class="mb-4">
            <p class="tb-section-label">Subject Notes</p>
            @foreach($subject->notes as $note)
                @php
                    $isLocked = shouldLockContent($note->created_at, $enrollment, $course);
                @endphp
                @if($isLocked)
                    {{-- Locked content - show pay to unlock message --}}
                    <div class="tb-item opacity-50 bg-gray-50">
                        <div class="tb-item-icon bg-gray-200">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <div class="tb-item-content">
                            <p class="tb-item-title text-gray-500">Locked Content</p>
                            <p class="tb-item-meta">Pay past month fee to unlock</p>
                        </div>
                        <a href="{{ route('student.payments.create', ['course_id' => $course->id, 'type' => 'past_month']) }}" class="text-xs bg-amber-100 text-amber-700 px-2 py-1 rounded font-medium">
                            Pay to Unlock
                        </a>
                    </div>
                @else
                    <div class="tb-item">
                        <div class="tb-item-icon tb-note-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="tb-item-content">
                            <p class="tb-item-title">{{ $note->title }}</p>
                            <p class="tb-item-meta">@if($note->user){{ $note->user->name }} • @endif{{ $note->created_at->diffForHumans() }}</p>
                        </div>
                        @if($note->file_url)
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
                        @endif
                    </div>
                @endif
                <hr class="tb-divider">
            @endforeach
        </div>
    @endif

    <!-- Subject Level Exams -->
    @php
        $subjectExams = $course->exams->where('subject_id', $subject->id)->whereNull('chapter_id');
    @endphp
    @if($subjectExams->count() > 0)
        <div class="mb-4">
            <p class="tb-exam-section-label">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Subject Exams
            </p>
            @foreach($subjectExams as $exam)
                @php
                    $isLocked = shouldLockContent($exam->created_at, $enrollment, $course);
                @endphp
                <div class="block tb-item {{ $isLocked ? 'opacity-60' : '' }}">
                    <div class="tb-item-icon tb-exam-icon {{ $isLocked ? 'bg-gray-200' : '' }}">
                        @if($isLocked)
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="tb-item-content">
                        <p class="tb-item-title {{ $isLocked ? 'text-gray-500' : '' }}">{{ $exam->title }}</p>
                        <p class="tb-item-meta">{{ $exam->total_questions }} questions &bull; {{ $exam->total_marks }} marks{{ $exam->duration_minutes ? ' &bull; ' . $exam->duration_minutes . ' min' : '' }}</p>
                    </div>
                    @if(!$isLocked)
                        <a href="{{ route('student.exams.show', $exam) }}" class="tb-btn-exam">
                            Start
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @else
                        <div class="text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                <hr class="tb-divider">
            @endforeach
        </div>
    @endif

    <!-- Subject Level Live Classes -->
    @php $subjectLiveClasses = $subject->liveClasses->whereNull('chapter_id')->whereNull('lesson_id'); @endphp
    @if($subjectLiveClasses->count() > 0)
        <div class="mb-4">
            <p class="tb-live-section-label">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Subject Live Classes
            </p>
            @foreach($subjectLiveClasses as $lc)
                @php
                    $isLocked = shouldLockContent($lc->created_at, $enrollment, $course);
                @endphp
                <div class="tb-item {{ $isLocked ? 'opacity-60' : '' }}">
                    <div class="tb-item-icon tb-live-icon {{ $isLocked ? 'bg-gray-200' : '' }}">
                        @if($isLocked)
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="tb-item-content">
                        <p class="tb-item-title {{ $isLocked ? 'text-gray-500' : '' }}">{{ $lc->title }}</p>
                        <p class="tb-item-meta">{{ $lc->scheduled_at->format('d M Y, h:i A') }} • {{ $lc->duration_minutes }} min • {{ $lc->platform_label }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(!$isLocked)
                            @if($lc->status === 'live')
                                {{-- Class is live - show LIVE badge and Join button --}}
                                <span class="tb-live-badge pulse">LIVE</span>
                                @if($lc->meeting_url)
                                    <a href="{{ $lc->secure_meeting_url }}" target="_blank" class="tb-btn-join">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        Join
                                    </a>
                                @endif
                            @elseif($lc->status === 'scheduled')
                                {{-- Class scheduled but not started yet - only show Upcoming --}}
                                <span class="tb-live-badge">Upcoming</span>
                                <span class="text-xs text-gray-400">Wait for teacher to start</span>
                            @elseif($lc->video_url)
                                {{-- Class ended but video available - show Watch button --}}
                                <span class="tb-live-badge">Recorded</span>
                                <a href="{{ $lc->video_url }}" target="_blank" class="tb-btn-watch" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    Watch
                                </a>
                            @else
                                <span class="tb-live-badge">{{ ucfirst($lc->status) }}</span>
                            @endif
                        @else
                            <div class="text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>
                <hr class="tb-divider">
            @endforeach
        </div>
    @endif

    <hr class="tb-level-divider">

    <!-- Chapters List -->
    <div class="mb-3">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h2 class="text-lg font-bold text-gray-900">Chapters</h2>
        </div>

        @if($subject->chapters->count() > 0)
            <div class="space-y-3">
                @foreach($subject->chapters as $chapter)
                    <a href="{{ route('student.chapter.show', ['course' => $course, 'subject' => $subject, 'chapter' => $chapter]) }}" class="block tb-chapter-card hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3">
                            <span class="tb-level-badge">{{ $loop->iteration }}</span>
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-gray-900">{{ $chapter->title }}</h3>
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                                @if($chapter->description)
                                    <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $chapter->description }}</p>
                                @endif
                                @php
                                    $chVideos = $chapter->contents->count() + $chapter->lessons->sum(fn($l) => $l->contents->count());
                                    $chNotes = $chapter->notes->count() + $chapter->lessons->sum(fn($l) => $l->notes->count());
                                    $chExams = $course->exams->where('chapter_id', $chapter->id)->count() + $course->exams->whereIn('lesson_id', $chapter->lessons->pluck('id'))->count();
                                @endphp
                                <div class="flex items-center gap-2 mt-2 flex-wrap">
                                    @if($chapter->lessons->count() > 0)
                                        <span class="tb-stat-pill yellow">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            {{ $chapter->lessons->count() }} Lessons
                                        </span>
                                    @endif
                                    @if($chVideos > 0)
                                        <span class="tb-stat-pill blue">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                            </svg>
                                            {{ $chVideos }} Videos
                                        </span>
                                    @endif
                                    @if($chNotes > 0)
                                        <span class="tb-stat-pill green">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            {{ $chNotes }} Notes
                                        </span>
                                    @endif
                                    @if($chExams > 0)
                                        <span class="tb-stat-pill" style="background: linear-gradient(135deg, #faf5ff, #f3e8ff); color: #6b21a8;">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                            </svg>
                                            {{ $chExams }} Exams
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No chapters available in this subject.</p>
        @endif
    </div>
</div>
@endsection
