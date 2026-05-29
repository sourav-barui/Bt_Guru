@extends('layouts.student_mobile')

@section('title', $course->title)

@push('styles')
<style>
    /* Vibrant Testbook Style */
    .tb-divider { height: 1px; background: linear-gradient(90deg, #e5e7eb, transparent); margin: 12px 0; }
    .tb-level-divider { height: 3px; background: linear-gradient(90deg, #7c3aed, #ec4899, #f59e0b, #10b981, #3b82f6); margin: 20px 0; border-radius: 2px; }
    
    /* Animated Header */
    .tb-course-header { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 50%, #ec4899 100%); color: white; padding: 24px 16px; margin: 0; border-radius: 0; position: relative; overflow: hidden; }
    .tb-course-header::before { content: ''; position: absolute; top: -50%; right: -20%; width: 200px; height: 200px; background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%); animation: pulse 3s ease-in-out infinite; }
    @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 0.5; } 50% { transform: scale(1.2); opacity: 0.8; } }
    
    /* Colorful Subject Cards */
    .tb-subject-card { background: white; border-radius: 20px; padding: 20px; margin-bottom: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border: 1px solid #f3f4f6; transition: all 0.3s ease; position: relative; overflow: hidden; }
    .tb-subject-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(124,58,237,0.2); }
    .tb-subject-card::after { content: ''; position: absolute; top: 0; right: 0; width: 100px; height: 100px; background: linear-gradient(135deg, rgba(124,58,237,0.05), transparent); border-radius: 0 0 0 100%; }
    
    /* Subject Icon Colors - Rainbow */
    .tb-icon-red { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; }
    .tb-icon-orange { background: linear-gradient(135deg, #ffedd5, #fed7aa); color: #ea580c; }
    .tb-icon-yellow { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #ca8a04; }
    .tb-icon-green { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669; }
    .tb-icon-cyan { background: linear-gradient(135deg, #cffafe, #a5f3fc); color: #0891b2; }
    .tb-icon-blue { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #2563eb; }
    .tb-icon-purple { background: linear-gradient(135deg, #e9d5ff, #d8b4fe); color: #7c3aed; }
    .tb-icon-pink { background: linear-gradient(135deg, #fce7f3, #fbcfe8); color: #db2777; }
    
    /* Badges */
    .tb-curriculum-badge { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: linear-gradient(135deg, #fbbf24, #f59e0b); color: white; border-radius: 12px; font-size: 14px; font-weight: 800; box-shadow: 0 4px 12px rgba(251,191,36,0.4); }
    
    /* Stats Pills */
    .tb-stat-pill { display: inline-flex; align-items: center; gap: 4px; padding: 6px 12px; background: #f3f4f6; border-radius: 20px; font-size: 12px; font-weight: 600; color: #4b5563; transition: all 0.2s; }
    .tb-stat-pill:hover { background: #e5e7eb; transform: scale(1.05); }
    .tb-stat-pill.blue { background: linear-gradient(135deg, #eff6ff, #dbeafe); color: #1d4ed8; }
    .tb-stat-pill.green { background: linear-gradient(135deg, #f0fdf4, #dcfce7); color: #15803d; }
    .tb-stat-pill.purple { background: linear-gradient(135deg, #faf5ff, #f3e8ff); color: #6b21a8; }
    
    /* Arrow Animation */
    .tb-arrow { transition: transform 0.3s ease; }
    .tb-subject-card:hover .tb-arrow { transform: translateX(4px); }
    
    /* Floating Shapes */
    .tb-float-shape { position: absolute; border-radius: 50%; opacity: 0.1; }
    
    /* Section Title */
    .tb-section-title { font-size: 18px; font-weight: 800; background: linear-gradient(135deg, #7c3aed, #ec4899); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
</style>
@endpush

@section('mobile-content')
<!-- Course Header -->
<div class="tb-course-header">
    <div class="flex items-start gap-4">
        <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
        </div>
        <div class="flex-1">
            <h1 class="text-xl font-bold">{{ $course->title }}</h1>
            <p class="text-white/80 text-sm mt-1">{{ $course->duration }}</p>
            @if($course->teachers->count() > 0)
                <p class="text-white/70 text-sm mt-1">{{ $course->teachers->pluck('name')->join(', ') }}</p>
            @endif
        </div>
    </div>
    <div class="mt-4 flex items-center gap-3">
        <span class="bg-green-400 text-green-900 px-3 py-1 rounded-full text-xs font-bold">Enrolled</span>
        <span class="text-xs text-white/70">Started {{ $enrollment->created_at->diffForHumans() }}</span>
    </div>
</div>

@php
    $courseLevelLiveClasses = $course->liveClasses
        ->whereNull('subject_id')
        ->whereNull('chapter_id')
        ->whereNull('lesson_id');
    $liveNow = $courseLevelLiveClasses->where('status','live');

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
@if($courseLevelLiveClasses->count() > 0)
<!-- Live Classes Section -->
<div class="px-3 pt-3 pb-1">

    @if($liveNow->count() > 0)
    <div class="mb-3 rounded-2xl overflow-hidden border-2 border-red-400" style="background:linear-gradient(135deg,#fef2f2,#fff5f5)">
        <div class="flex items-center gap-2 px-4 py-2" style="background:linear-gradient(90deg,#ef4444,#dc2626)">
            <span class="inline-block w-2 h-2 rounded-full bg-white animate-ping"></span>
            <span class="text-white font-bold text-sm tracking-wide">LIVE NOW</span>
        </div>
        @foreach($liveNow as $lc)
        @php
            $isLocked = shouldLockContent($lc->created_at, $enrollment, $course);
        @endphp
        <div class="px-4 py-3 {{ $isLocked ? 'opacity-60' : '' }}">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <p class="font-bold text-gray-900">{{ $lc->title }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $lc->platform_label }} &bull; {{ $lc->duration_minutes }} min</p>
                    @if($lc->meeting_id)
                        <p class="text-xs text-gray-500 mt-0.5">ID: {{ $lc->meeting_id }}{{ $lc->meeting_password ? ' &bull; Pass: ' . $lc->meeting_password : '' }}</p>
                    @endif
                </div>
                @if($isLocked)
                    <div class="flex-shrink-0 ml-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                @endif
            </div>
            @if(!$isLocked)
                <a href="{{ $lc->meeting_url }}" target="_blank"
                   class="mt-2 inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-sm text-white"
                   style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Join Live Class Now
                </a>
            @else
                <div class="mt-2 inline-flex items-center gap-2 px-4 py-2 rounded-xl font-bold text-sm text-white bg-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Pay Monthly Fee to Access
                </div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

    @php $upcoming = $courseLevelLiveClasses->where('status','scheduled'); @endphp
    @if($upcoming->count() > 0)
    <div class="mb-3">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 px-1">Upcoming Live Classes</p>
        <div class="space-y-2">
            @foreach($upcoming as $lc)
            @php
                $isLocked = shouldLockContent($lc->created_at, $enrollment, $course);
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-4 py-3 flex items-center gap-3 {{ $isLocked ? 'opacity-60' : '' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#fee2e2,#fecaca)">
                    <svg class="w-5 h-5" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $lc->title }}</p>
                    <p class="text-xs text-gray-500">{{ $lc->scheduled_at->format('d M, h:i A') }} &bull; {{ $lc->platform_label }}</p>
                    @if($lc->meeting_id)
                        <p class="text-xs text-gray-400">ID: {{ $lc->meeting_id }}{{ $lc->meeting_password ? ' &bull; Pass: ' . $lc->meeting_password : '' }}</p>
                    @endif
                </div>
                @if($isLocked)
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                @else
                    {{-- Scheduled classes - no Join button until teacher starts --}}
                    <span class="text-xs text-gray-400">Upcoming</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @php $recorded = $courseLevelLiveClasses->where('status','completed')->whereNotNull('video_url'); @endphp
    @if($recorded->count() > 0)
    <div class="mb-3">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 px-1">📹 Recorded Classes</p>
        <div class="space-y-2">
            @foreach($recorded as $lc)
            @php
                $isLocked = shouldLockContent($lc->created_at, $enrollment, $course);
            @endphp
            <div class="bg-white rounded-2xl border border-blue-100 shadow-sm px-4 py-3 flex items-center gap-3 {{ $isLocked ? 'opacity-60' : '' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#dbeafe,#3b82f6)">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $lc->title }}</p>
                    <p class="text-xs text-gray-500">{{ $lc->scheduled_at->format('d M Y') }} &bull; {{ $lc->duration_minutes }} min</p>
                </div>
                @if($isLocked)
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                @else
                    <a href="{{ $lc->video_url }}" target="_blank"
                       class="flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-bold text-white"
                       style="background:linear-gradient(135deg,#3b82f6,#2563eb)">
                        Watch
                    </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
<div class="mx-3 mb-2" style="height:3px;background:linear-gradient(90deg,#ef4444,#f97316,#eab308,#22c55e,#3b82f6,#8b5cf6);border-radius:2px"></div>
@endif

@php
    $courseLevelExams = $course->exams
        ->whereNull('subject_id')
        ->whereNull('chapter_id')
        ->whereNull('lesson_id');
@endphp
@if($courseLevelExams->count() > 0)
<!-- Exams Section -->
<div class="px-3 pt-3 pb-1">
    <div class="mb-3">
        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-2 px-1">📝 Course Exams</p>
        <div class="space-y-2">
            @foreach($courseLevelExams as $exam)
            @php
                $isLocked = shouldLockContent($exam->created_at, $enrollment, $course);
                $isEnded = $exam->end_time && $exam->end_time < now();
            @endphp
            <div class="block bg-white rounded-2xl border {{ $isEnded ? 'border-gray-200' : 'border-purple-200' }} shadow-sm px-4 py-3 flex items-center gap-3 {{ ($isLocked || $isEnded) ? 'opacity-60' : 'hover:border-purple-400 transition-colors' }}">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $isEnded ? 'bg-gray-100' : '' }}" @if(!$isEnded) style="background:linear-gradient(135deg,#e9d5ff,#d8b4fe)" @endif>
                    @if($isEnded)
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="#7c3aed" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">{{ $exam->title }}</p>
                    <p class="text-xs text-gray-500">{{ $exam->total_questions }} questions &bull; {{ $exam->total_marks }} marks</p>
                    @if($exam->duration_minutes)
                        <p class="text-xs text-gray-400">Duration: {{ $exam->duration_minutes }} min</p>
                    @endif
                </div>
                @if($isLocked)
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                @elseif($isEnded)
                    <span class="flex-shrink-0 px-2 py-1 bg-red-100 text-red-600 text-xs font-bold rounded-lg">Ended</span>
                @else
                    <a href="{{ route('student.exams.show', $exam) }}" class="flex-shrink-0 px-3 py-1.5 rounded-xl text-xs font-bold text-white no-underline" style="background:linear-gradient(135deg,#7c3aed,#6d28d9)">
                        Start
                    </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
<div class="mx-3 mb-2" style="height:3px;background:linear-gradient(90deg,#7c3aed,#8b5cf6,#a78bfa);border-radius:2px"></div>
@endif

<!-- Subjects List -->
<div class="p-3">
    <div class="flex items-center gap-2 mb-4 px-1">
        <span class="tb-section-title">📚 Select Subject</span>
    </div>

    @forelse($course->curricula as $curriculum)
        <div class="mb-6">
            <div class="flex items-center gap-3 mb-4 px-1">
                <span class="tb-curriculum-badge">{{ $loop->iteration }}</span>
                <span class="font-bold text-gray-800 text-lg">{{ $curriculum->title }}</span>
            </div>
            
            @if($curriculum->subjects->count() > 0)
                <div class="space-y-4">
                    @foreach($curriculum->subjects as $subject)
                        @php
                            $colors = ['tb-icon-red', 'tb-icon-orange', 'tb-icon-yellow', 'tb-icon-green', 'tb-icon-cyan', 'tb-icon-blue', 'tb-icon-purple', 'tb-icon-pink'];
                            $iconColor = $colors[$loop->index % count($colors)];
                        @endphp
                        <a href="{{ route('student.subject.show', ['course' => $course, 'subject' => $subject]) }}" class="block tb-subject-card">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 {{ $iconColor }} rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-gray-900 text-lg">{{ $subject->title }}</h3>
                                        <svg class="w-5 h-5 text-gray-400 tb-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </div>
                                    @if($subject->description)
                                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $subject->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-3 flex-wrap">
                                        @php
                                            $totalVideos = $subject->contents->count();
                                            $totalNotes = $subject->notes->count();
                                            $totalExams = $course->exams->where('subject_id', $subject->id)->whereNull('chapter_id')->count();
                                            foreach($subject->chapters as $chapter) {
                                                $totalVideos += $chapter->contents->count();
                                                $totalVideos += $chapter->lessons->sum(function($l) { return $l->contents->count(); });
                                                $totalNotes += $chapter->notes->count();
                                                $totalNotes += $chapter->lessons->sum(function($l) { return $l->notes->count(); });
                                                $totalExams += $course->exams->where('chapter_id', $chapter->id)->count();
                                                $totalExams += $course->exams->whereIn('lesson_id', $chapter->lessons->pluck('id'))->count();
                                            }
                                        @endphp
                                        @if($subject->chapters->count() > 0)
                                            <span class="tb-stat-pill purple">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                </svg>
                                                {{ $subject->chapters->count() }} Chapters
                                            </span>
                                        @endif
                                        @if($totalVideos > 0)
                                            <span class="tb-stat-pill blue">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                </svg>
                                                {{ $totalVideos }} Videos
                                            </span>
                                        @endif
                                        @if($totalNotes > 0)
                                            <span class="tb-stat-pill green">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                {{ $totalNotes }} Notes
                                            </span>
                                        @endif
                                        @if($totalExams > 0)
                                            <span class="tb-stat-pill" style="background:linear-gradient(135deg,#faf5ff,#f3e8ff);color:#6b21a8;">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                                {{ $totalExams }} Exams
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 bg-gray-50 rounded-xl">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <p class="text-gray-500">No subjects available yet.</p>
                </div>
            @endif
        </div>
        <hr class="tb-level-divider">
    @empty
        <div class="text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
            <p class="text-gray-500 text-lg">No curriculum content available yet.</p>
        </div>
    @endforelse
</div>
@endsection
