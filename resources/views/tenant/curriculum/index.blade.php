@extends('layouts.tenant')

@section('title', 'Curriculum - ' . $course->title)
@section('page-title', 'Curriculum: ' . $course->title)

@section('page-content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <a href="{{ route('tenant.courses.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1 mb-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Courses
            </a>
            <p class="text-sm text-gray-500">Manage course curriculum structure</p>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Live Classes button --}}
            @php
                $liveCount = $course->liveClasses()->where('status','scheduled')->where('scheduled_at','>',now())->count()
                           + $course->liveClasses()->where('status','live')->count();
            @endphp
            <a href="{{ route('tenant.live_classes.index', $course) }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-red-200 bg-red-50 text-red-700 font-semibold text-sm hover:bg-red-100 transition-colors">
                <span class="w-2.5 h-2.5 rounded-full {{ $liveCount > 0 ? 'bg-red-500 animate-pulse' : 'bg-red-300' }}"></span>
                Live Classes
                @if($liveCount > 0)
                    <span class="bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">{{ $liveCount }}</span>
                @endif
            </a>
            <a href="{{ route('tenant.curriculum.curricula.create', $course) }}" class="btn-primary inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Curriculum Section
            </a>
        </div>
    </div>

    @if($course->curricula->count() === 0)
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No Curriculum Yet</h3>
            <p class="text-gray-500 mb-4">Start building your course curriculum by adding sections.</p>
            <a href="{{ route('tenant.curriculum.curricula.create', $course) }}" class="btn-primary inline-flex">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add First Section
            </a>
        </div>
    @else
        <!-- Curriculum Structure -->
        <div class="space-y-4">
            @foreach($course->curricula as $curriculum)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Curriculum Header -->
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center text-white font-bold">
                                    {{ $loop->iteration }}
                                </span>
                                <div>
                                    <h3 class="font-semibold text-white">{{ $curriculum->title }}</h3>
                                    @if($curriculum->description)
                                        <p class="text-sm text-blue-100">{{ $curriculum->description }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="badge {{ $curriculum->status === 'active' ? 'bg-green-100 text-green-800' : ($curriculum->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                    {{ ucfirst($curriculum->status) }}
                                </span>
                                <div class="flex items-center gap-1">
                                    <a href="{{ route('tenant.curriculum.curricula.edit', [$course, $curriculum]) }}" class="p-2 text-white/80 hover:text-white hover:bg-white/20 rounded-lg transition-colors" title="Edit">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('tenant.curriculum.subjects.create', [$course, $curriculum]) }}" class="p-2 text-white/80 hover:text-white hover:bg-white/20 rounded-lg transition-colors" title="Add Subject">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('tenant.curriculum.curricula.destroy', [$course, $curriculum]) }}" class="inline" onsubmit="return confirm('Delete this curriculum section and all its contents?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-white/80 hover:text-white hover:bg-white/20 rounded-lg transition-colors" title="Delete">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subjects -->
                    <div class="divide-y divide-gray-200">
                        @forelse($curriculum->subjects as $subject)
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <h4 class="font-medium text-gray-900">{{ $subject->title }}</h4>
                                        <span class="badge {{ $subject->status === 'active' ? 'badge-success' : ($subject->status === 'draft' ? 'badge-warning' : 'badge-danger') }}">
                                            {{ ucfirst($subject->status) }}
                                        </span>
                                        @php $subjectExams = $subject->exams()->count(); @endphp
                                        @if($subjectExams > 0)
                                            <a href="{{ route('tenant.exams.index', $course) }}?subject={{ $subject->id }}" class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium hover:bg-purple-200" title="{{ $subjectExams }} Exam(s)">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                </svg>
                                                {{ $subjectExams }}
                                            </a>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button onclick="toggleContentForm('subject-live-{{ $subject->id }}')" class="p-1.5 hover:bg-red-50 rounded" title="Schedule Live Class" style="color:#ef4444">
                                            <svg class="w-4 h-4" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </button>
                                        <a href="{{ route('tenant.btlive.create', $course) }}?subject_id={{ $subject->id }}" class="p-1.5 hover:bg-red-50 rounded" title="New BTLive" style="color:#dc2626">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        </a>
                                        <button onclick="toggleContentForm('subject-video-{{ $subject->id }}')" class="p-1.5 text-blue-400 hover:text-blue-600 hover:bg-blue-50 rounded" title="Add Video">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="toggleContentForm('subject-note-{{ $subject->id }}')" class="p-1.5 text-green-400 hover:text-green-600 hover:bg-green-50 rounded" title="Add Note">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </button>
                                        <a href="{{ route('tenant.exams.create', $course) }}?level=subject&level_id={{ $subject->id }}" class="p-1.5 text-purple-400 hover:text-purple-600 hover:bg-purple-50 rounded" title="Add Exam">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('tenant.curriculum.subjects.edit', [$course, $subject]) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded" title="Edit Subject">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('tenant.curriculum.chapters.create', [$course, $subject]) }}" class="p-1.5 text-gray-400 hover:text-green-600 hover:bg-green-50 rounded" title="Add Chapter">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('tenant.curriculum.subjects.destroy', [$course, $subject]) }}" class="inline" onsubmit="return confirm('Delete this subject and all its contents?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded" title="Delete Subject">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                @if($subject->description)
                                    <p class="text-sm text-gray-600 mb-3">{{ $subject->description }}</p>
                                @endif

                                <!-- Chapters -->
                                <div class="ml-6 space-y-2">
                                    @forelse($subject->chapters as $chapter)
                                        <div class="border-l-2 border-gray-200 pl-4 py-2">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <span class="font-medium text-gray-800">{{ $chapter->title }}</span>
                                                    <span class="badge {{ $chapter->status === 'active' ? 'badge-success' : ($chapter->status === 'draft' ? 'badge-warning' : 'badge-danger') }} text-xs">
                                                        {{ ucfirst($chapter->status) }}
                                                    </span>
                                                    @php $chapterExams = $chapter->exams()->count(); @endphp
                                                    @if($chapterExams > 0)
                                                        <a href="{{ route('tenant.exams.index', $course) }}?chapter={{ $chapter->id }}" class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium hover:bg-purple-200" title="{{ $chapterExams }} Exam(s)">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                            </svg>
                                                            {{ $chapterExams }}
                                                        </a>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <button onclick="toggleContentForm('chapter-live-{{ $chapter->id }}')" class="p-1 hover:bg-red-50 rounded" title="Schedule Live Class" style="color:#ef4444">
                                                        <svg class="w-4 h-4" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    </button>
                                                    <a href="{{ route('tenant.btlive.create', $course) }}?chapter_id={{ $chapter->id }}" class="p-1 hover:bg-red-50 rounded" title="New BTLive" style="color:#dc2626">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    </a>
                                                    <button onclick="toggleContentForm('video-{{ $chapter->id }}')" class="p-1 text-blue-400 hover:text-blue-600" title="Add Video">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </button>
                                                    <button onclick="toggleContentForm('note-{{ $chapter->id }}')" class="p-1 text-green-400 hover:text-green-600" title="Add Note">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </button>
                                                    <a href="{{ route('tenant.exams.create', $course) }}?level=chapter&level_id={{ $chapter->id }}" class="p-1 text-purple-400 hover:text-purple-600" title="Add Exam">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('tenant.curriculum.chapters.edit', [$course, $chapter]) }}" class="p-1 text-gray-400 hover:text-blue-600" title="Edit Chapter">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                    <form method="POST" action="{{ route('tenant.curriculum.chapters.destroy', [$course, $chapter]) }}" class="inline" onsubmit="return confirm('Delete this chapter and all its content?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600" title="Delete Chapter">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>

                                            @if($chapter->description)
                                                <p class="text-sm text-gray-600 mb-2">{{ $chapter->description }}</p>
                                            @endif

                                            <!-- Lessons -->
                                            <div class="ml-4 space-y-2">
                                                @forelse($chapter->lessons as $lesson)
                                                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg border border-gray-200">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                            <span class="text-sm font-medium text-gray-800">{{ $lesson->title }}</span>
                                                            @if($lesson->duration_minutes)
                                                                <span class="text-xs text-gray-500">{{ $lesson->duration_minutes }} min</span>
                                                            @endif
                                                            <span class="badge {{ $lesson->status === 'active' ? 'badge-success' : ($lesson->status === 'draft' ? 'badge-warning' : 'badge-danger') }} text-xs">
                                                                {{ ucfirst($lesson->status) }}
                                                            </span>
                                                            @php $lessonExams = $lesson->exams()->count(); @endphp
                                                            @if($lessonExams > 0)
                                                                <a href="{{ route('tenant.exams.index', $course) }}?lesson={{ $lesson->id }}" class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium hover:bg-purple-200" title="{{ $lessonExams }} Exam(s)">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                                    </svg>
                                                                    {{ $lessonExams }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <button onclick="toggleContentForm('lesson-live-{{ $lesson->id }}')" class="p-1 hover:bg-red-50 rounded" title="Schedule Live Class" style="color:#ef4444">
                                                                <svg class="w-4 h-4" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                            </button>
                                                            <a href="{{ route('tenant.btlive.create', $course) }}?lesson_id={{ $lesson->id }}" class="p-1 hover:bg-red-50 rounded" title="New BTLive" style="color:#dc2626">
                                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                            </a>
                                                            <button onclick="toggleContentForm('lesson-video-{{ $lesson->id }}')" class="p-1 text-blue-400 hover:text-blue-600" title="Add Video">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                                </svg>
                                                            </button>
                                                            <button onclick="toggleContentForm('lesson-note-{{ $lesson->id }}')" class="p-1 text-green-400 hover:text-green-600" title="Add Note">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                            </button>
                                                            <a href="{{ route('tenant.exams.create', $course) }}?level=lesson&level_id={{ $lesson->id }}" class="p-1 text-purple-400 hover:text-purple-600" title="Add Exam">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                                </svg>
                                                            </a>
                                                            <a href="{{ route('tenant.curriculum.lessons.edit', [$course, $lesson]) }}" class="p-1 text-gray-400 hover:text-blue-600" title="Edit Lesson">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </a>
                                                            <form method="POST" action="{{ route('tenant.curriculum.lessons.destroy', [$course, $lesson]) }}" class="inline" onsubmit="return confirm('Delete this lesson?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="p-1 text-gray-400 hover:text-red-600" title="Delete Lesson">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    {{-- Lesson Live Classes --}}
                                                    @foreach($lesson->liveClasses as $lc)
                                                        <div class="ml-4 flex items-center justify-between py-1.5 px-3 bg-red-50 rounded-lg">
                                                            <div class="flex items-center gap-2">
                                                                <svg class="w-3 h-3" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                                <div>
                                                                    <span class="text-sm text-gray-800">{{ $lc->title }}</span>
                                                                    <div class="text-xs text-gray-500">{{ $lc->scheduled_at->format('d M Y, h:i A') }} · {{ $lc->duration_minutes }}min · {{ $lc->platform_label }}@if($lc->meeting_password) <span class="text-amber-600" title="Password protected: {{ $lc->meeting_password }}">🔒</span>@endif</div>
                                                                </div>
                                                                <span class="text-xs font-bold px-1.5 py-0.5 rounded-full {{ $lc->status_badge }}">{{ $lc->status_label }}</span>
                                                            </div>
                                                            <div class="flex items-center gap-1">
                                                                @if($lc->status === 'scheduled')
                                                                    {{-- Start button for scheduled classes --}}
                                                                    <form method="POST" action="{{ route('tenant.live_classes.markLive', [$course, $lc]) }}" class="inline">
                                                                        @csrf
                                                                        <button type="submit" class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded hover:bg-red-600 flex items-center gap-1">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                            START
                                                                        </button>
                                                                    </form>
                                                                @elseif($lc->status === 'live')
                                                                    {{-- Live: Show END button + Open link --}}
                                                                    <form method="POST" action="{{ route('tenant.live_classes.endLive', [$course, $lc]) }}" class="inline">
                                                                        @csrf
                                                                        <button type="submit" class="px-2 py-1 bg-black text-white text-xs font-bold rounded hover:bg-gray-900 flex items-center gap-1 shadow-md border border-gray-800" onclick="return confirm('End this live class?')">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                                                                            END
                                                                        </button>
                                                                    </form>
                                                                    <a href="{{ $lc->secure_meeting_url }}" target="_blank" class="p-1 text-indigo-500 hover:text-indigo-700" title="Open Meeting{{ $lc->meeting_password ? ' (Password: ' . $lc->meeting_password . ')' : '' }}"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>
                                                                @elseif($lc->status === 'completed')
                                                                    {{-- Completed: Show START AGAIN + UPLOAD VIDEO --}}
                                                                    <form method="POST" action="{{ route('tenant.live_classes.markLive', [$course, $lc]) }}" class="inline">
                                                                        @csrf
                                                                        <button type="submit" class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded hover:bg-green-600 flex items-center gap-1">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                            RESTART
                                                                        </button>
                                                                    </form>
                                                                    @if($lc->video_url)
                                                                        <a href="{{ $lc->video_url }}" target="_blank" class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded flex items-center gap-1" title="View Uploaded Video">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                                            VIDEO
                                                                        </a>
                                                                        {{-- Edit Video Button --}}
                                                                        <button onclick="document.getElementById('video-edit-lesson-{{ $lc->id }}').classList.toggle('hidden')" class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded flex items-center gap-1" title="Edit Video Link">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                            EDIT
                                                                        </button>
                                                                    @else
                                                                        <button onclick="document.getElementById('video-upload-lesson-{{ $lc->id }}').classList.toggle('hidden')" class="px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 flex items-center gap-1 shadow-md">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                                            UPLOAD
                                                                        </button>
                                                                    @endif
                                                                @endif
                                                                <a href="{{ route('tenant.live_classes.edit', [$course, $lc]) }}" class="p-1 text-gray-400 hover:text-blue-600" title="Edit"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                                                <form method="POST" action="{{ route('tenant.live_classes.destroy', [$course, $lc]) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="p-1 text-gray-400 hover:text-red-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                                            </div>
                                                        </div>
                                                        {{-- Video Upload/Edit Forms for Lesson --}}
                                                        @if($lc->status === 'completed')
                                                            @if(!$lc->video_url)
                                                                <div id="video-upload-lesson-{{ $lc->id }}" class="hidden ml-4 mt-2 p-2 bg-purple-50 rounded">
                                                                    <form method="POST" action="{{ route('tenant.live_classes.uploadVideo', [$course, $lc]) }}" class="flex gap-2">
                                                                        @csrf
                                                                        <input type="url" name="video_url" placeholder="Enter video URL (YouTube/Drive/etc.)" class="flex-1 text-xs px-2 py-1 border rounded" required>
                                                                        <button type="submit" class="px-2 py-1 bg-purple-600 text-white text-xs font-bold rounded">SAVE</button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                            @if($lc->video_url)
                                                                <div id="video-edit-lesson-{{ $lc->id }}" class="hidden ml-4 mt-2 p-2 bg-amber-50 rounded">
                                                                    <form method="POST" action="{{ route('tenant.live_classes.uploadVideo', [$course, $lc]) }}" class="flex gap-2">
                                                                        @csrf
                                                                        <input type="url" name="video_url" value="{{ $lc->video_url }}" placeholder="Enter video URL (YouTube/Drive/etc.)" class="flex-1 text-xs px-2 py-1 border rounded" required>
                                                                        <button type="submit" class="px-2 py-1 bg-amber-600 text-white text-xs font-bold rounded">UPDATE</button>
                                                                    </form>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach

                                                    {{-- Lesson Add Live Class Form --}}
                                                    <div id="lesson-live-{{ $lesson->id }}" class="hidden ml-4 p-2 bg-red-50 rounded-lg">
                                                        <form method="POST" action="{{ route('tenant.live_classes.store', $course) }}" class="space-y-2">
                                                            @csrf
                                                            <input type="hidden" name="lesson_id" value="{{ $lesson->id }}">
                                                            <input type="hidden" name="subject_id" value="{{ $lesson->chapter->subject_id ?? '' }}">
                                                            <input type="hidden" name="chapter_id" value="{{ $lesson->chapter_id }}">
                                                            <input type="hidden" name="redirect" value="curriculum">
                                                            <input type="hidden" name="status" value="scheduled">
                                                            <input type="hidden" name="recurrence" value="none">
                                                            <input type="text" name="title" placeholder="Live class title" class="form-input text-xs" required>
                                                            <div class="grid grid-cols-2 gap-2">
                                                                <select name="platform" class="form-input text-xs">
                                                                    <option value="google_meet">Google Meet</option>
                                                                    <option value="zoom">Zoom</option>
                                                                    <option value="ms_teams">MS Teams</option>
                                                                    <option value="jitsi">Jitsi Meet</option>
                                                                    <option value="other">Other</option>
                                                                </select>
                                                                <input type="url" name="meeting_url" placeholder="Meeting URL" class="form-input text-xs" required>
                                                            </div>
                                                            <input type="text" name="meeting_password" placeholder="Password / Passcode (optional)" class="form-input text-xs">
                                                            <div class="grid grid-cols-2 gap-2">
                                                                <input type="datetime-local" name="scheduled_at" class="form-input text-xs" required>
                                                                <input type="number" name="duration_minutes" value="60" placeholder="Duration (min)" class="form-input text-xs" min="5" max="480" required>
                                                            </div>
                                                            <label class="flex items-center gap-2 text-xs cursor-pointer">
                                                                <input type="checkbox" name="is_public" value="1" class="w-4 h-4 text-indigo-600 rounded">
                                                                <span class="text-indigo-700 font-medium">Public (visible to all students)</span>
                                                            </label>
                                                            <div class="flex gap-2">
                                                                <button type="submit" class="btn-primary text-xs py-1 px-2">Schedule</button>
                                                                <button type="button" onclick="toggleContentForm('lesson-live-{{ $lesson->id }}')" class="btn-secondary text-xs py-1 px-2">Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    {{-- Lesson Videos & Notes --}}
                                                    @foreach($lesson->contents as $content)
                                                        <div class="ml-4 flex items-center justify-between py-1.5 px-3 bg-blue-50 rounded-lg">
                                                            <div class="flex items-center gap-2">
                                                                <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                                </svg>
                                                                <div>
                                                                    <span class="text-sm text-gray-800">{{ $content->title }}</span>
                                                                    <div class="text-xs text-gray-500">
                                                                        @if($content->user)by {{ $content->user->name }} • @endif{{ $content->created_at->diffForHumans() }}
                                                                    </div>
                                                                </div>
                                                                @if($content->video_type)
                                                                    <span class="text-xs text-blue-600 bg-blue-100 px-1.5 py-0.5 rounded">{{ ucfirst($content->video_type) }}</span>
                                                                @endif
                                                            </div>
                                                            <div class="flex items-center gap-1">
                                                                @if($content->video_url)
                                                                    <a href="{{ $content->video_url }}" target="_blank" class="p-1 text-blue-400 hover:text-blue-600" title="Watch">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                        </svg>
                                                                    </a>
                                                                @endif
                                                                <form method="POST" action="{{ route('tenant.curriculum.content.destroy', [$course, $content]) }}" class="inline" onsubmit="return confirm('Delete?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="p-1 text-gray-400 hover:text-red-600">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                        </svg>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    @foreach($lesson->notes as $note)
                                                        <div class="ml-4 flex items-center justify-between py-1.5 px-3 bg-green-50 rounded-lg">
                                                            <div class="flex items-center gap-2">
                                                                <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                                <div>
                                                                    <span class="text-sm text-gray-800">{{ $note->title }}</span>
                                                                    <div class="text-xs text-gray-500">
                                                                        @if($note->user)by {{ $note->user->name }} • @endif{{ $note->created_at->diffForHumans() }}
                                                                    </div>
                                                                </div>
                                                                <span class="text-xs text-green-600 bg-green-100 px-1.5 py-0.5 rounded">PDF</span>
                                                            </div>
                                                            <div class="flex items-center gap-1">
                                                                <a href="{{ $note->file_url }}" target="_blank" class="p-1 text-green-400 hover:text-green-600">
                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                                    </svg>
                                                                </a>
                                                                <form method="POST" action="{{ route('tenant.curriculum.notes.destroy', [$course, $note]) }}" class="inline" onsubmit="return confirm('Delete?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="p-1 text-gray-400 hover:text-red-600">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                        </svg>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    {{-- Lesson Add Video Form --}}
                                                    <div id="lesson-video-{{ $lesson->id }}" class="hidden ml-4 p-2 bg-blue-50 rounded-lg">
                                                        <form method="POST" action="{{ route('tenant.curriculum.content.store', $course) }}" class="space-y-2">
                                                            @csrf
                                                            <input type="hidden" name="contentable_type" value="App\Models\Lesson">
                                                            <input type="hidden" name="contentable_id" value="{{ $lesson->id }}">
                                                            <input type="text" name="title" placeholder="Video Title" class="form-input text-xs" required>
                                                            <div class="grid grid-cols-2 gap-2">
                                                                <select name="video_type" class="form-input text-xs">
                                                                    <option value="youtube">YouTube</option>
                                                                    <option value="vimeo">Vimeo</option>
                                                                    <option value="other">Other</option>
                                                                </select>
                                                                <input type="url" name="video_url" placeholder="Video URL" class="form-input text-xs" required>
                                                            </div>
                                                            <div class="flex gap-2">
                                                                <button type="submit" class="btn-primary text-xs py-1 px-2">Add</button>
                                                                <button type="button" onclick="toggleContentForm('lesson-video-{{ $lesson->id }}')" class="btn-secondary text-xs py-1 px-2">Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    {{-- Lesson Add Note Form --}}
                                                    <div id="lesson-note-{{ $lesson->id }}" class="hidden ml-4 p-2 bg-green-50 rounded-lg">
                                                        <form method="POST" action="{{ route('tenant.curriculum.notes.store', $course) }}" enctype="multipart/form-data" class="space-y-2">
                                                            @csrf
                                                            <input type="hidden" name="noteable_type" value="App\Models\Lesson">
                                                            <input type="hidden" name="noteable_id" value="{{ $lesson->id }}">
                                                            <input type="text" name="title" placeholder="Note Title" class="form-input text-xs" required>
                                                            <input type="file" name="file" accept=".pdf" class="form-input text-xs" required>
                                                            <label class="flex items-center gap-2 text-xs cursor-pointer">
                                                                <input type="checkbox" name="is_downloadable" value="1" class="w-4 h-4 text-green-600 rounded">
                                                                <span class="text-green-700 font-medium">Allow Download</span>
                                                            </label>
                                                            <div class="flex gap-2">
                                                                <button type="submit" class="btn-primary text-xs py-1 px-2">Add PDF</button>
                                                                <button type="button" onclick="toggleContentForm('lesson-note-{{ $lesson->id }}')" class="btn-secondary text-xs py-1 px-2">Cancel</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @empty
                                                    <p class="text-sm text-gray-400 italic ml-6">No lessons yet</p>
                                                @endforelse

                                                {{-- Chapter Add Lesson Button --}}
                                                <div class="mt-2 ml-6">
                                                    <a href="{{ route('tenant.curriculum.lessons.create', [$course, $chapter]) }}" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                        Add Lesson
                                                    </a>
                                                </div>
                                            </div>

                                            <!-- Videos & Notes -->
                                            <div class="ml-4 space-y-2">
                                                {{-- Chapter Live Classes --}}
                                                @foreach($chapter->liveClasses->where('lesson_id', null) as $lc)
                                                    <div class="flex items-center justify-between py-2 px-3 bg-red-50 rounded-lg">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                            <div>
                                                                <span class="text-sm font-medium text-gray-800">{{ $lc->title }}</span>
                                                                <div class="text-xs text-gray-500">{{ $lc->scheduled_at->format('d M Y, h:i A') }} · {{ $lc->duration_minutes }}min · {{ $lc->platform_label }}@if($lc->meeting_password) <span class="text-amber-600" title="Password protected: {{ $lc->meeting_password }}">🔒</span>@endif</div>
                                                            </div>
                                                            <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $lc->status_badge }}">{{ $lc->status_label }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            @if($lc->status === 'scheduled')
                                                                {{-- Start button for scheduled classes --}}
                                                                <form method="POST" action="{{ route('tenant.live_classes.markLive', [$course, $lc]) }}" class="inline">
                                                                    @csrf
                                                                    <button type="submit" class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded hover:bg-red-600 flex items-center gap-1">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                        START
                                                                    </button>
                                                                </form>
                                                            @elseif($lc->status === 'live')
                                                                {{-- Live: Show END button + Open link --}}
                                                                <form method="POST" action="{{ route('tenant.live_classes.endLive', [$course, $lc]) }}" class="inline">
                                                                    @csrf
                                                                    <button type="submit" class="px-2 py-1 bg-black text-white text-xs font-bold rounded hover:bg-gray-900 flex items-center gap-1 shadow-md border border-gray-800" onclick="return confirm('End this live class?')">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                                                                        END
                                                                    </button>
                                                                </form>
                                                                <a href="{{ $lc->secure_meeting_url }}" target="_blank" class="p-1 text-indigo-500 hover:text-indigo-700" title="Open Meeting{{ $lc->meeting_password ? ' (Password: ' . $lc->meeting_password . ')' : '' }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>
                                                            @elseif($lc->status === 'completed')
                                                                {{-- Completed: Show START AGAIN + UPLOAD VIDEO --}}
                                                                <form method="POST" action="{{ route('tenant.live_classes.markLive', [$course, $lc]) }}" class="inline">
                                                                    @csrf
                                                                    <button type="submit" class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded hover:bg-green-600 flex items-center gap-1">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                        RESTART
                                                                    </button>
                                                                </form>
                                                                @if($lc->video_url)
                                                                    <a href="{{ $lc->video_url }}" target="_blank" class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded flex items-center gap-1" title="View Uploaded Video">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                                        VIDEO
                                                                    </a>
                                                                    {{-- Edit Video Button --}}
                                                                    <button onclick="document.getElementById('video-edit-chapter-{{ $lc->id }}').classList.toggle('hidden')" class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded flex items-center gap-1" title="Edit Video Link">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                        EDIT
                                                                    </button>
                                                                @else
                                                                    <button onclick="document.getElementById('video-upload-chapter-{{ $lc->id }}').classList.toggle('hidden')" class="px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 flex items-center gap-1 shadow-md">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                                        UPLOAD
                                                                    </button>
                                                                @endif
                                                            @endif
                                                            <a href="{{ route('tenant.live_classes.edit', [$course, $lc]) }}" class="p-1 text-gray-400 hover:text-blue-600" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                                            <form method="POST" action="{{ route('tenant.live_classes.destroy', [$course, $lc]) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="p-1 text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                                        </div>
                                                    </div>
                                                    {{-- Video Upload/Edit Forms for Chapter --}}
                                                    @if($lc->status === 'completed')
                                                        @if(!$lc->video_url)
                                                            <div id="video-upload-chapter-{{ $lc->id }}" class="hidden mt-2 p-2 bg-purple-50 rounded">
                                                                <form method="POST" action="{{ route('tenant.live_classes.uploadVideo', [$course, $lc]) }}" class="flex gap-2">
                                                                    @csrf
                                                                    <input type="url" name="video_url" placeholder="Enter video URL (YouTube/Drive/etc.)" class="flex-1 text-xs px-2 py-1 border rounded" required>
                                                                    <button type="submit" class="px-2 py-1 bg-purple-600 text-white text-xs font-bold rounded">SAVE</button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                        @if($lc->video_url)
                                                            <div id="video-edit-chapter-{{ $lc->id }}" class="hidden mt-2 p-2 bg-amber-50 rounded">
                                                                <form method="POST" action="{{ route('tenant.live_classes.uploadVideo', [$course, $lc]) }}" class="flex gap-2">
                                                                    @csrf
                                                                    <input type="url" name="video_url" value="{{ $lc->video_url }}" placeholder="Enter video URL (YouTube/Drive/etc.)" class="flex-1 text-xs px-2 py-1 border rounded" required>
                                                                    <button type="submit" class="px-2 py-1 bg-amber-600 text-white text-xs font-bold rounded">UPDATE</button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    @endif
                                                @endforeach

                                                {{-- Chapter Add Live Class Form --}}
                                                <div id="chapter-live-{{ $chapter->id }}" class="hidden mt-2 p-3 bg-red-50 rounded-lg">
                                                    <form method="POST" action="{{ route('tenant.live_classes.store', $course) }}" class="space-y-2">
                                                        @csrf
                                                        <input type="hidden" name="chapter_id" value="{{ $chapter->id }}">
                                                        <input type="hidden" name="subject_id" value="{{ $chapter->subject_id }}">
                                                        <input type="hidden" name="redirect" value="curriculum">
                                                        <input type="hidden" name="status" value="scheduled">
                                                        <input type="hidden" name="recurrence" value="none">
                                                        <input type="text" name="title" placeholder="Live class title" class="form-input text-sm" required>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <select name="platform" class="form-input text-sm">
                                                                <option value="google_meet">Google Meet</option>
                                                                <option value="zoom">Zoom</option>
                                                                <option value="ms_teams">MS Teams</option>
                                                                <option value="jitsi">Jitsi Meet</option>
                                                                <option value="other">Other</option>
                                                            </select>
                                                            <input type="url" name="meeting_url" placeholder="Meeting URL" class="form-input text-sm" required>
                                                        </div>
                                                        <input type="text" name="meeting_password" placeholder="Password / Passcode (optional)" class="form-input text-sm">
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <input type="datetime-local" name="scheduled_at" class="form-input text-sm" required>
                                                            <input type="number" name="duration_minutes" value="60" placeholder="Duration (min)" class="form-input text-sm" min="5" max="480" required>
                                                        </div>
                                                        <label class="flex items-center gap-2 text-xs cursor-pointer">
                                                            <input type="checkbox" name="is_public" value="1" class="w-4 h-4 text-indigo-600 rounded">
                                                            <span class="text-indigo-700 font-medium">Public (visible to all students)</span>
                                                        </label>
                                                        <div class="flex gap-2">
                                                            <button type="submit" class="btn-primary text-sm py-1.5">Schedule</button>
                                                            <button type="button" onclick="toggleContentForm('chapter-live-{{ $chapter->id }}')" class="btn-secondary text-sm py-1.5">Cancel</button>
                                                        </div>
                                                    </form>
                                                </div>

                                                {{-- Video Content --}}
                                                @foreach($chapter->contents as $content)
                                                    <div class="flex items-center justify-between py-2 px-3 bg-blue-50 rounded-lg">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                            </svg>
                                                            <div>
                                                                <span class="text-sm font-medium text-gray-800">{{ $content->title }}</span>
                                                                <div class="text-xs text-gray-500">
                                                                    @if($content->user)by {{ $content->user->name }} • @endif{{ $content->created_at->diffForHumans() }}
                                                                </div>
                                                            </div>
                                                            @if($content->video_type)
                                                                <span class="text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded">{{ ucfirst($content->video_type) }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            @if($content->video_url)
                                                                <a href="{{ $content->video_url }}" target="_blank" class="p-1 text-blue-400 hover:text-blue-600" title="Watch Video">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                    </svg>
                                                                </a>
                                                            @endif
                                                            <form method="POST" action="{{ route('tenant.curriculum.content.destroy', [$course, $content]) }}" class="inline" onsubmit="return confirm('Delete this video?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="p-1 text-gray-400 hover:text-red-600" title="Delete Video">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                {{-- PDF Notes --}}
                                                @foreach($chapter->notes as $note)
                                                    <div class="flex items-center justify-between py-2 px-3 bg-green-50 rounded-lg">
                                                        <div class="flex items-center gap-2">
                                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            <div>
                                                                <span class="text-sm font-medium text-gray-800">{{ $note->title }}</span>
                                                                <div class="text-xs text-gray-500">
                                                                    @if($note->user)by {{ $note->user->name }} • @endif{{ $note->created_at->diffForHumans() }}
                                                                </div>
                                                            </div>
                                                            <span class="text-xs text-green-600 bg-green-100 px-2 py-0.5 rounded">PDF</span>
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <a href="{{ $note->file_url }}" target="_blank" class="p-1 text-green-400 hover:text-green-600" title="Download PDF">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                                </svg>
                                                            </a>
                                                            <form method="POST" action="{{ route('tenant.curriculum.notes.destroy', [$course, $note]) }}" class="inline" onsubmit="return confirm('Delete this note?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="p-1 text-gray-400 hover:text-red-600" title="Delete Note">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                @endforeach

                                                @if($chapter->contents->count() === 0 && $chapter->notes->count() === 0)
                                                    <p class="text-sm text-gray-400 italic ml-6">No content yet. Click video or note icons above to add.</p>
                                                @endif
                                            </div>

                                            {{-- Add Video Form --}}
                                            <div id="video-{{ $chapter->id }}" class="hidden ml-4 mt-2 p-3 bg-blue-50 rounded-lg">
                                                <form method="POST" action="{{ route('tenant.curriculum.content.store', $course) }}" class="space-y-2">
                                                    @csrf
                                                    <input type="hidden" name="contentable_type" value="App\Models\Chapter">
                                                    <input type="hidden" name="contentable_id" value="{{ $chapter->id }}">
                                                    <input type="text" name="title" placeholder="Video Title" class="form-input text-sm" required>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <select name="video_type" class="form-input text-sm">
                                                            <option value="youtube">YouTube</option>
                                                            <option value="vimeo">Vimeo</option>
                                                            <option value="other">Other</option>
                                                        </select>
                                                        <input type="url" name="video_url" placeholder="Video URL" class="form-input text-sm" required>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <button type="submit" class="btn-primary text-sm py-1.5">Add Video</button>
                                                        <button type="button" onclick="toggleContentForm('video-{{ $chapter->id }}')" class="btn-secondary text-sm py-1.5">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>

                                            {{-- Add Note Form --}}
                                            <div id="note-{{ $chapter->id }}" class="hidden ml-4 mt-2 p-3 bg-green-50 rounded-lg">
                                                <form method="POST" action="{{ route('tenant.curriculum.notes.store', $course) }}" enctype="multipart/form-data" class="space-y-2">
                                                    @csrf
                                                    <input type="hidden" name="noteable_type" value="App\Models\Chapter">
                                                    <input type="hidden" name="noteable_id" value="{{ $chapter->id }}">
                                                    <input type="text" name="title" placeholder="Note Title" class="form-input text-sm" required>
                                                    <input type="file" name="file" accept=".pdf" class="form-input text-sm" required>
                                                    <label class="flex items-center gap-2 text-xs cursor-pointer">
                                                        <input type="checkbox" name="is_downloadable" value="1" class="w-4 h-4 text-green-600 rounded">
                                                        <span class="text-green-700 font-medium">Allow Download</span>
                                                    </label>
                                                    <div class="flex gap-2">
                                                        <button type="submit" class="btn-primary text-sm py-1.5">Add PDF</button>
                                                        <button type="button" onclick="toggleContentForm('note-{{ $chapter->id }}')" class="btn-secondary text-sm py-1.5">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-400 italic ml-6">No chapters yet</p>
                                    @endforelse
                                </div>

                                <!-- Subject Level Content -->
                                @if($subject->contents->count() > 0 || $subject->notes->count() > 0 || $subject->liveClasses->where('chapter_id', null)->count() > 0)
                                    <div class="mt-3 ml-4 space-y-2 border-t border-gray-100 pt-3">
                                        {{-- Subject Live Classes --}}
                                        @foreach($subject->liveClasses->where('chapter_id', null) as $lc)
                                            <div class="flex items-center justify-between py-2 px-3 bg-red-50 rounded-lg">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-800">{{ $lc->title }}</span>
                                                        <div class="text-xs text-gray-500">{{ $lc->scheduled_at->format('d M Y, h:i A') }} · {{ $lc->duration_minutes }}min · {{ $lc->platform_label }}@if($lc->meeting_password) <span class="text-amber-600" title="Password protected: {{ $lc->meeting_password }}">🔒</span>@endif</div>
                                                    </div>
                                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $lc->status_badge }}">{{ $lc->status_label }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    @if($lc->status === 'scheduled')
                                                        {{-- Scheduled: Show START button --}}
                                                        <form method="POST" action="{{ route('tenant.live_classes.markLive', [$course, $lc]) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded hover:bg-red-600 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                START
                                                            </button>
                                                        </form>
                                                    @elseif($lc->status === 'live')
                                                        {{-- Live: Show END button + Open link --}}
                                                        <form method="POST" action="{{ route('tenant.live_classes.endLive', [$course, $lc]) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="px-2 py-1 bg-black text-white text-xs font-bold rounded hover:bg-gray-900 flex items-center gap-1 shadow-md border border-gray-800" onclick="return confirm('End this live class?')">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                                                                END
                                                            </button>
                                                        </form>
                                                        <a href="{{ $lc->secure_meeting_url }}" target="_blank" class="p-1 text-indigo-500 hover:text-indigo-700" title="Open Meeting{{ $lc->meeting_password ? ' (Password: ' . $lc->meeting_password . ')' : '' }}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>
                                                    @elseif($lc->status === 'completed')
                                                        {{-- Completed: Show START AGAIN + UPLOAD VIDEO --}}
                                                        <form method="POST" action="{{ route('tenant.live_classes.markLive', [$course, $lc]) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="px-2 py-1 bg-green-500 text-white text-xs font-bold rounded hover:bg-green-600 flex items-center gap-1">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                RESTART
                                                            </button>
                                                        </form>
                                                        @if($lc->video_url)
                                                            {{-- Video uploaded --}}
                                                            <a href="{{ $lc->video_url }}" target="_blank" class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded flex items-center gap-1" title="View Uploaded Video">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                                VIDEO
                                                            </a>
                                                            {{-- Edit Video Button --}}
                                                            <button onclick="document.getElementById('video-edit-{{ $lc->id }}').classList.toggle('hidden')" class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded flex items-center gap-1" title="Edit Video Link">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                                EDIT
                                                            </button>
                                                        @else
                                                            {{-- Upload video button --}}
                                                            <button onclick="document.getElementById('video-upload-{{ $lc->id }}').classList.toggle('hidden')" class="px-2 py-1 bg-blue-600 text-white text-xs font-bold rounded hover:bg-blue-700 flex items-center gap-1 shadow-md">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                                UPLOAD
                                                            </button>
                                                        @endif
                                                    @endif
                                                    <a href="{{ route('tenant.live_classes.edit', [$course, $lc]) }}" class="p-1 text-gray-400 hover:text-blue-600" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                                    <form method="POST" action="{{ route('tenant.live_classes.destroy', [$course, $lc]) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="p-1 text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                                </div>
                                                {{-- Video Upload Form --}}
                                                @if($lc->status === 'completed')
                                                    {{-- Upload Video Form --}}
                                                    @if(!$lc->video_url)
                                                        <div id="video-upload-{{ $lc->id }}" class="hidden mt-2 p-2 bg-purple-50 rounded">
                                                            <form method="POST" action="{{ route('tenant.live_classes.uploadVideo', [$course, $lc]) }}" class="flex gap-2">
                                                                @csrf
                                                                <input type="url" name="video_url" placeholder="Enter video URL (YouTube/Drive/etc.)" class="flex-1 text-xs px-2 py-1 border rounded" required>
                                                                <button type="submit" class="px-2 py-1 bg-purple-600 text-white text-xs font-bold rounded">SAVE</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                    {{-- Edit Video Form --}}
                                                    @if($lc->video_url)
                                                        <div id="video-edit-{{ $lc->id }}" class="hidden mt-2 p-2 bg-amber-50 rounded">
                                                            <form method="POST" action="{{ route('tenant.live_classes.uploadVideo', [$course, $lc]) }}" class="flex gap-2">
                                                                @csrf
                                                                <input type="url" name="video_url" value="{{ $lc->video_url }}" placeholder="Enter video URL (YouTube/Drive/etc.)" class="flex-1 text-xs px-2 py-1 border rounded" required>
                                                                <button type="submit" class="px-2 py-1 bg-amber-600 text-white text-xs font-bold rounded">UPDATE</button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        @endforeach

                                        {{-- Subject Videos --}}
                                        @foreach($subject->contents as $content)
                                            <div class="flex items-center justify-between py-2 px-3 bg-blue-50 rounded-lg">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-800">{{ $content->title }}</span>
                                                        <div class="text-xs text-gray-500">
                                                            @if($content->user)by {{ $content->user->name }} • @endif{{ $content->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                    @if($content->video_type)
                                                        <span class="text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded">{{ ucfirst($content->video_type) }}</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    @if($content->video_url)
                                                        <a href="{{ $content->video_url }}" target="_blank" class="p-1 text-blue-400 hover:text-blue-600" title="Watch Video">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    <form method="POST" action="{{ route('tenant.curriculum.content.destroy', [$course, $content]) }}" class="inline" onsubmit="return confirm('Delete this video?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600" title="Delete Video">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach

                                        {{-- Subject Notes --}}
                                        @foreach($subject->notes as $note)
                                            <div class="flex items-center justify-between py-2 px-3 bg-green-50 rounded-lg">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-800">{{ $note->title }}</span>
                                                        <div class="text-xs text-gray-500">
                                                            @if($note->user)by {{ $note->user->name }} • @endif{{ $note->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                    <span class="text-xs text-green-600 bg-green-100 px-2 py-0.5 rounded">PDF</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <a href="{{ $note->file_url }}" target="_blank" class="p-1 text-green-400 hover:text-green-600" title="Download PDF">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                        </svg>
                                                    </a>
                                                    <form method="POST" action="{{ route('tenant.curriculum.notes.destroy', [$course, $note]) }}" class="inline" onsubmit="return confirm('Delete this note?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600" title="Delete Note">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                <!-- Subject Add Live Class Form -->
                                <div id="subject-live-{{ $subject->id }}" class="hidden ml-4 mt-2 p-3 bg-red-50 rounded-lg">
                                    <form method="POST" action="{{ route('tenant.live_classes.store', $course) }}" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                                        <input type="hidden" name="redirect" value="curriculum">
                                        <input type="hidden" name="status" value="scheduled">
                                        <input type="hidden" name="recurrence" value="none">
                                        <input type="text" name="title" placeholder="Live class title" class="form-input text-sm" required>
                                        <div class="grid grid-cols-2 gap-2">
                                            <select name="platform" class="form-input text-sm">
                                                <option value="google_meet">Google Meet</option>
                                                <option value="zoom">Zoom</option>
                                                <option value="ms_teams">MS Teams</option>
                                                <option value="jitsi">Jitsi Meet</option>
                                                <option value="other">Other</option>
                                            </select>
                                            <input type="url" name="meeting_url" placeholder="Meeting URL" class="form-input text-sm" required>
                                        </div>
                                        <input type="text" name="meeting_password" placeholder="Password / Passcode (optional)" class="form-input text-sm">
                                        <div class="grid grid-cols-2 gap-2">
                                            <input type="datetime-local" name="scheduled_at" class="form-input text-sm" required>
                                            <input type="number" name="duration_minutes" value="60" placeholder="Duration (min)" class="form-input text-sm" min="5" max="480" required>
                                        </div>
                                        <label class="flex items-center gap-2 text-xs cursor-pointer">
                                            <input type="checkbox" name="is_public" value="1" class="w-4 h-4 text-indigo-600 rounded">
                                            <span class="text-indigo-700 font-medium">Public (visible to all students)</span>
                                        </label>
                                        <div class="flex gap-2">
                                            <button type="submit" class="btn-primary text-sm py-1.5">Schedule</button>
                                            <button type="button" onclick="toggleContentForm('subject-live-{{ $subject->id }}')" class="btn-secondary text-sm py-1.5">Cancel</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Subject Add Video Form -->
                                <div id="subject-video-{{ $subject->id }}" class="hidden ml-4 mt-2 p-3 bg-blue-50 rounded-lg">
                                    <form method="POST" action="{{ route('tenant.curriculum.content.store', $course) }}" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="contentable_type" value="App\Models\Subject">
                                        <input type="hidden" name="contentable_id" value="{{ $subject->id }}">
                                        <input type="text" name="title" placeholder="Video Title" class="form-input text-sm" required>
                                        <div class="grid grid-cols-2 gap-2">
                                            <select name="video_type" class="form-input text-sm">
                                                <option value="youtube">YouTube</option>
                                                <option value="vimeo">Vimeo</option>
                                                <option value="other">Other</option>
                                            </select>
                                            <input type="url" name="video_url" placeholder="Video URL" class="form-input text-sm" required>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit" class="btn-primary text-sm py-1.5">Add Video</button>
                                            <button type="button" onclick="toggleContentForm('subject-video-{{ $subject->id }}')" class="btn-secondary text-sm py-1.5">Cancel</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Subject Add Note Form -->
                                <div id="subject-note-{{ $subject->id }}" class="hidden ml-4 mt-2 p-3 bg-green-50 rounded-lg">
                                    <form method="POST" action="{{ route('tenant.curriculum.notes.store', $course) }}" enctype="multipart/form-data" class="space-y-2">
                                        @csrf
                                        <input type="hidden" name="noteable_type" value="App\Models\Subject">
                                        <input type="hidden" name="noteable_id" value="{{ $subject->id }}">
                                        <input type="text" name="title" placeholder="Note Title" class="form-input text-sm" required>
                                        <input type="file" name="file" accept=".pdf" class="form-input text-sm" required>
                                        <label class="flex items-center gap-2 text-xs cursor-pointer">
                                            <input type="checkbox" name="is_downloadable" value="1" class="w-4 h-4 text-green-600 rounded">
                                            <span class="text-green-700 font-medium">Allow Download</span>
                                        </label>
                                        <div class="flex gap-2">
                                            <button type="submit" class="btn-primary text-sm py-1.5">Add PDF</button>
                                            <button type="button" onclick="toggleContentForm('subject-note-{{ $subject->id }}')" class="btn-secondary text-sm py-1.5">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-4 text-center">
                                <p class="text-gray-500 mb-2">No subjects in this section yet</p>
                                <a href="{{ route('tenant.curriculum.subjects.create', [$course, $curriculum]) }}" class="text-sm text-blue-600 hover:text-blue-700">
                                    Add Subject
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
function toggleContentForm(id) {
    const element = document.getElementById(id);
    if (element) {
        element.classList.toggle('hidden');
    }
}
</script>
@endsection
