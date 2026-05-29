@extends('layouts.teacher')

@section('title', $course->title)
@section('page-title', $course->title)

@section('page-content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Course Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h2>
                <p class="text-gray-600 mt-2">{{ $course->description }}</p>
                <div class="flex items-center gap-4 mt-4 text-sm text-gray-500">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ $course->duration ?? 'Duration not set' }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        ₹{{ number_format($course->fees) }}
                    </span>
                    <span class="badge {{ $course->status === 'active' ? 'badge-success' : 'badge-warning' }}">
                        {{ ucfirst($course->status) }}
                    </span>
                </div>
            </div>
            @if($course->thumbnail)
                <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="w-32 h-24 object-cover rounded-lg">
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <button onclick="alert('Attendance feature coming soon!')" class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 text-center hover:border-blue-300 transition-colors">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Take Attendance</span>
        </button>

        <a href="#curriculum-section" class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 text-center hover:border-green-300 transition-colors block">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Manage Curriculum</span>
        </a>

        <a href="{{ route('teacher.exams.index', $course) }}" class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 text-center hover:border-purple-300 transition-colors block">
            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Manage Exams</span>
        </a>

        <button onclick="alert('Progress report feature coming soon!')" class="bg-white rounded-xl p-4 shadow-sm border border-gray-200 text-center hover:border-blue-300 transition-colors">
            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <span class="text-sm font-medium text-gray-700">Progress Report</span>
        </button>
    </div>

    <!-- Curriculum Management -->
    <div id="curriculum-section" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Course Curriculum</h3>
                <p class="text-sm text-gray-500">Manage subjects, chapters, lessons, videos and notes</p>
            </div>
            <a href="{{ route('teacher.curriculum.createCurriculum', $course) }}" class="btn-primary text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Section
            </a>
        </div>

        @forelse($course->curricula as $curriculum)
            <div class="mb-6 border border-gray-200 rounded-lg overflow-hidden">
                <!-- Curriculum Header -->
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $curriculum->title }}</h4>
                                @if($curriculum->description)
                                    <p class="text-sm text-gray-600">{{ $curriculum->description }}</p>
                                @endif
                            </div>
                            <span class="badge {{ $curriculum->status === 'active' ? 'badge-success' : ($curriculum->status === 'draft' ? 'badge-warning' : 'badge-danger') }}">
                                {{ ucfirst($curriculum->status) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('teacher.curriculum.editCurriculum', [$course, $curriculum]) }}" class="p-1.5 text-gray-400 hover:text-blue-600" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('teacher.curriculum.createSubject', [$course, $curriculum]) }}" class="p-1.5 text-gray-400 hover:text-green-600" title="Add Subject">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('teacher.curriculum.destroyCurriculum', [$course, $curriculum]) }}" class="inline" onsubmit="return confirm('Delete this section and all its contents?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Subjects -->
                <div class="divide-y divide-gray-100">
                    @forelse($curriculum->subjects as $subject)
                        <div class="px-4 py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="font-medium text-gray-800">{{ $subject->title }}</span>
                                    <span class="badge {{ $subject->status === 'active' ? 'badge-success' : ($subject->status === 'draft' ? 'badge-warning' : 'badge-danger') }} text-xs">
                                        {{ ucfirst($subject->status) }}
                                    </span>
                                    @php $subjectExams = $subject->exams()->count(); @endphp
                                    @if($subjectExams > 0)
                                        <a href="{{ route('teacher.exams.index', $course) }}" class="inline-flex items-center gap-1 px-2 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium hover:bg-purple-200" title="{{ $subjectExams }} Exam(s)">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                            {{ $subjectExams }}
                                        </a>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1">
                                    <button onclick="toggleContentForm('subject-live-{{ $subject->id }}')" class="p-1 hover:bg-red-50 rounded" title="Schedule Live Class" style="color:#ef4444">
                                        <svg class="w-4 h-4" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                    <button onclick="toggleContentForm('subject-video-{{ $subject->id }}')" class="p-1 text-blue-400 hover:text-blue-600" title="Add Video">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="toggleContentForm('subject-note-{{ $subject->id }}')" class="p-1 text-green-400 hover:text-green-600" title="Add Note">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </button>
                                    <a href="{{ route('teacher.exams.create', $course) }}?level=subject&level_id={{ $subject->id }}" class="p-1 text-purple-400 hover:text-purple-600 hover:bg-purple-50 rounded" title="Add Exam">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                    </a>
                                    <a href="{{ route('teacher.curriculum.editSubject', [$course, $subject]) }}" class="p-1 text-gray-400 hover:text-blue-600" title="Edit Subject">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('teacher.curriculum.createChapter', [$course, $subject]) }}" class="p-1 text-gray-400 hover:text-green-600" title="Add Chapter">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('teacher.curriculum.destroySubject', [$course, $subject]) }}" class="inline" onsubmit="return confirm('Delete this subject?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-gray-400 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Subject Videos & Notes -->
                            @if($subject->contents->count() > 0 || $subject->notes->count() > 0 || $subject->liveClasses->where('chapter_id', null)->count() > 0)
                                <div class="ml-6 mt-3 space-y-2 border-t border-gray-100 pt-3">
                                    {{-- Subject Live Classes --}}
                                    @foreach($subject->liveClasses->where('chapter_id', null) as $lc)
                                        <div class="flex items-center justify-between py-2 px-3 bg-red-50 rounded-lg">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                <div>
                                                    <span class="text-sm font-medium text-gray-800">{{ $lc->title }}</span>
                                                    <div class="text-xs text-gray-500">{{ $lc->scheduled_at->format('d M Y, h:i A') }} · {{ $lc->duration_minutes }}min · {{ $lc->platform_label }}</div>
                                                </div>
                                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $lc->status_badge }}">{{ $lc->status_label }}</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <a href="{{ $lc->meeting_url }}" target="_blank" class="p-1 text-indigo-500 hover:text-indigo-700" title="Open"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>
                                                <a href="{{ route('teacher.live_classes.edit', [$course, $lc]) }}" class="p-1 text-gray-400 hover:text-blue-600" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                                <form method="POST" action="{{ route('teacher.live_classes.destroy', [$course, $lc]) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="p-1 text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                            </div>
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
                                                <form method="POST" action="{{ route('teacher.curriculum.destroyContent', [$course, $content]) }}" class="inline" onsubmit="return confirm('Delete this video?')">
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
                                                <form method="POST" action="{{ route('teacher.curriculum.destroyNote', [$course, $note]) }}" class="inline" onsubmit="return confirm('Delete this note?')">
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
                            <div id="subject-live-{{ $subject->id }}" class="hidden ml-6 mt-2 p-3 bg-red-50 rounded-lg">
                                <form method="POST" action="{{ route('teacher.live_classes.store', $course) }}" class="space-y-2">
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
                                    <div class="grid grid-cols-2 gap-2">
                                        <input type="datetime-local" name="scheduled_at" class="form-input text-sm" required>
                                        <input type="number" name="duration_minutes" value="60" placeholder="Duration (min)" class="form-input text-sm" min="5" max="480" required>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit" class="btn-primary text-sm py-1.5">Schedule</button>
                                        <button type="button" onclick="toggleContentForm('subject-live-{{ $subject->id }}')" class="btn-secondary text-sm py-1.5">Cancel</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Subject Add Video Form -->
                            <div id="subject-video-{{ $subject->id }}" class="hidden ml-6 mt-2 p-3 bg-blue-50 rounded-lg">
                                <form method="POST" action="{{ route('teacher.curriculum.storeContent', $course) }}" class="space-y-2">
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
                            <div id="subject-note-{{ $subject->id }}" class="hidden ml-6 mt-2 p-3 bg-green-50 rounded-lg">
                                <form method="POST" action="{{ route('teacher.curriculum.storeNote', $course) }}" enctype="multipart/form-data" class="space-y-2">
                                    @csrf
                                    <input type="hidden" name="noteable_type" value="App\Models\Subject">
                                    <input type="hidden" name="noteable_id" value="{{ $subject->id }}">
                                    <input type="text" name="title" placeholder="Note Title" class="form-input text-sm" required>
                                    <input type="file" name="file" accept=".pdf" class="form-input text-sm" required>
                                    <div class="flex gap-2">
                                        <button type="submit" class="btn-primary text-sm py-1.5">Add PDF</button>
                                        <button type="button" onclick="toggleContentForm('subject-note-{{ $subject->id }}')" class="btn-secondary text-sm py-1.5">Cancel</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Chapters -->
                            <div class="ml-6 mt-2 space-y-2">
                                @forelse($subject->chapters as $chapter)
                                    <div class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded-lg">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-700">{{ $chapter->title }}</span>
                                            @if($chapter->contents->count() > 0)
                                                <span class="text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded">{{ $chapter->contents->count() }} videos</span>
                                            @endif
                                            @if($chapter->notes->count() > 0)
                                                <span class="text-xs text-green-600 bg-green-100 px-2 py-0.5 rounded">{{ $chapter->notes->count() }} notes</span>
                                            @endif
                                            @php $chapterExams = $chapter->exams()->count(); @endphp
                                            @if($chapterExams > 0)
                                                <a href="{{ route('teacher.exams.index', $course) }}" class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium hover:bg-purple-200" title="{{ $chapterExams }} Exam(s)">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                                    {{ $chapterExams }}
                                                </a>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button onclick="toggleContentForm('chapter-live-{{ $chapter->id }}')" class="p-1 hover:bg-red-50 rounded" title="Schedule Live Class" style="color:#ef4444">
                                                <svg class="w-4 h-4" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            </button>
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
                                            <a href="{{ route('teacher.exams.create', $course) }}?level=chapter&level_id={{ $chapter->id }}" class="p-1 text-purple-400 hover:text-purple-600 hover:bg-purple-50 rounded" title="Add Exam">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                            </a>
                                            <a href="{{ route('teacher.curriculum.editChapter', [$course, $chapter]) }}" class="p-1 text-gray-400 hover:text-blue-600">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form method="POST" action="{{ route('teacher.curriculum.destroyChapter', [$course, $chapter]) }}" class="inline" onsubmit="return confirm('Delete this chapter?')">
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
                                                        <a href="{{ route('teacher.exams.index', $course) }}" class="inline-flex items-center gap-1 px-1.5 py-0.5 bg-purple-100 text-purple-700 rounded-full text-xs font-medium hover:bg-purple-200" title="{{ $lessonExams }} Exam(s)">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                                            {{ $lessonExams }}
                                                        </a>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <button onclick="toggleContentForm('lesson-live-{{ $lesson->id }}')" class="p-1 hover:bg-red-50 rounded" title="Schedule Live Class" style="color:#ef4444">
                                                        <svg class="w-4 h-4" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    </button>
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
                                                    <a href="{{ route('teacher.exams.create', $course) }}?level=lesson&level_id={{ $lesson->id }}" class="p-1 text-purple-400 hover:text-purple-600 hover:bg-purple-50 rounded" title="Add Exam">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                                    </a>
                                                    <a href="{{ route('teacher.curriculum.editLesson', [$course, $lesson]) }}" class="p-1 text-gray-400 hover:text-blue-600" title="Edit Lesson">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                    </a>
                                                    <form method="POST" action="{{ route('teacher.curriculum.destroyLesson', [$course, $lesson]) }}" class="inline" onsubmit="return confirm('Delete this lesson?')">
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
                                                            <div class="text-xs text-gray-500">{{ $lc->scheduled_at->format('d M Y, h:i A') }} · {{ $lc->duration_minutes }}min · {{ $lc->platform_label }}</div>
                                                        </div>
                                                        <span class="text-xs font-bold px-1.5 py-0.5 rounded-full {{ $lc->status_badge }}">{{ $lc->status_label }}</span>
                                                    </div>
                                                    <div class="flex items-center gap-1">
                                                        <a href="{{ $lc->meeting_url }}" target="_blank" class="p-1 text-indigo-500 hover:text-indigo-700" title="Open"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>
                                                        <a href="{{ route('teacher.live_classes.edit', [$course, $lc]) }}" class="p-1 text-gray-400 hover:text-blue-600" title="Edit"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                                        <form method="POST" action="{{ route('teacher.live_classes.destroy', [$course, $lc]) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="p-1 text-gray-400 hover:text-red-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                                    </div>
                                                </div>
                                            @endforeach

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
                                                        <form method="POST" action="{{ route('teacher.curriculum.destroyContent', [$course, $content]) }}" class="inline" onsubmit="return confirm('Delete?')">
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
                                                        <form method="POST" action="{{ route('teacher.curriculum.destroyNote', [$course, $note]) }}" class="inline" onsubmit="return confirm('Delete?')">
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

                                            {{-- Lesson Add Live Class Form --}}
                                            <div id="lesson-live-{{ $lesson->id }}" class="hidden ml-4 p-2 bg-red-50 rounded-lg">
                                                <form method="POST" action="{{ route('teacher.live_classes.store', $course) }}" class="space-y-2">
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
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <input type="datetime-local" name="scheduled_at" class="form-input text-xs" required>
                                                        <input type="number" name="duration_minutes" value="60" placeholder="Duration (min)" class="form-input text-xs" min="5" max="480" required>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <button type="submit" class="btn-primary text-xs py-1 px-2">Schedule</button>
                                                        <button type="button" onclick="toggleContentForm('lesson-live-{{ $lesson->id }}')" class="btn-secondary text-xs py-1 px-2">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>

                                            {{-- Lesson Add Video Form --}}
                                            <div id="lesson-video-{{ $lesson->id }}" class="hidden ml-4 p-2 bg-blue-50 rounded-lg">
                                                <form method="POST" action="{{ route('teacher.curriculum.storeContent', $course) }}" class="space-y-2">
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
                                                <form method="POST" action="{{ route('teacher.curriculum.storeNote', $course) }}" enctype="multipart/form-data" class="space-y-2">
                                                    @csrf
                                                    <input type="hidden" name="noteable_type" value="App\Models\Lesson">
                                                    <input type="hidden" name="noteable_id" value="{{ $lesson->id }}">
                                                    <input type="text" name="title" placeholder="Note Title" class="form-input text-xs" required>
                                                    <input type="file" name="file" accept=".pdf" class="form-input text-xs" required>
                                                    <div class="flex gap-2">
                                                        <button type="submit" class="btn-primary text-xs py-1 px-2">Add PDF</button>
                                                        <button type="button" onclick="toggleContentForm('lesson-note-{{ $lesson->id }}')" class="btn-secondary text-xs py-1 px-2">Cancel</button>
                                                    </div>
                                                </form>
                                            </div>
                                        @empty
                                            <p class="text-sm text-gray-400 italic ml-6">No lessons yet</p>
                                        @endforelse

                                        {{-- Add Lesson Button --}}
                                        <div class="mt-2 ml-6">
                                            <a href="{{ route('teacher.curriculum.createLesson', [$course, $chapter]) }}" class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-700">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                Add Lesson
                                            </a>
                                        </div>
                                    </div>

                                    <!-- Chapter Videos & Notes -->
                                    <div class="ml-4 space-y-2 border-t border-gray-100 pt-3">
                                        {{-- Chapter Live Classes --}}
                                        @foreach($chapter->liveClasses->where('lesson_id', null) as $lc)
                                            <div class="flex items-center justify-between py-2 px-3 bg-red-50 rounded-lg">
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="#ef4444" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                    <div>
                                                        <span class="text-sm font-medium text-gray-800">{{ $lc->title }}</span>
                                                        <div class="text-xs text-gray-500">{{ $lc->scheduled_at->format('d M Y, h:i A') }} · {{ $lc->duration_minutes }}min · {{ $lc->platform_label }}</div>
                                                    </div>
                                                    <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $lc->status_badge }}">{{ $lc->status_label }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <a href="{{ $lc->meeting_url }}" target="_blank" class="p-1 text-indigo-500 hover:text-indigo-700" title="Open"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg></a>
                                                    <a href="{{ route('teacher.live_classes.edit', [$course, $lc]) }}" class="p-1 text-gray-400 hover:text-blue-600" title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg></a>
                                                    <form method="POST" action="{{ route('teacher.live_classes.destroy', [$course, $lc]) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="p-1 text-gray-400 hover:text-red-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></form>
                                                </div>
                                            </div>
                                        @endforeach

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
                                                    <form method="POST" action="{{ route('teacher.curriculum.destroyContent', [$course, $content]) }}" class="inline" onsubmit="return confirm('Delete this video?')">
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
                                                    <form method="POST" action="{{ route('teacher.curriculum.destroyNote', [$course, $note]) }}" class="inline" onsubmit="return confirm('Delete this note?')">
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

                                        @if($chapter->contents->count() === 0 && $chapter->notes->count() === 0 && $chapter->liveClasses->where('lesson_id', null)->count() === 0)
                                            <p class="text-sm text-gray-400 italic">No content yet. Click icons above to add.</p>
                                        @endif
                                    </div>

                                    {{-- Chapter Add Live Class Form --}}
                                    <div id="chapter-live-{{ $chapter->id }}" class="hidden ml-4 mt-2 p-3 bg-red-50 rounded-lg">
                                        <form method="POST" action="{{ route('teacher.live_classes.store', $course) }}" class="space-y-2">
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
                                            <div class="grid grid-cols-2 gap-2">
                                                <input type="datetime-local" name="scheduled_at" class="form-input text-sm" required>
                                                <input type="number" name="duration_minutes" value="60" placeholder="Duration (min)" class="form-input text-sm" min="5" max="480" required>
                                            </div>
                                            <div class="flex gap-2">
                                                <button type="submit" class="btn-primary text-sm py-1.5">Schedule</button>
                                                <button type="button" onclick="toggleContentForm('chapter-live-{{ $chapter->id }}')" class="btn-secondary text-sm py-1.5">Cancel</button>
                                            </div>
                                        </form>
                                    </div>

                                    {{-- Add Video Form --}}
                                    <div id="video-{{ $chapter->id }}" class="hidden ml-4 mt-2 p-3 bg-blue-50 rounded-lg">
                                        <form method="POST" action="{{ route('teacher.curriculum.storeContent', $course) }}" class="space-y-2">
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
                                        <form method="POST" action="{{ route('teacher.curriculum.storeNote', $course) }}" enctype="multipart/form-data" class="space-y-2">
                                            @csrf
                                            <input type="hidden" name="noteable_type" value="App\Models\Chapter">
                                            <input type="hidden" name="noteable_id" value="{{ $chapter->id }}">
                                            <input type="text" name="title" placeholder="Note Title" class="form-input text-sm" required>
                                            <input type="file" name="file" accept=".pdf" class="form-input text-sm" required>
                                            <div class="flex gap-2">
                                                <button type="submit" class="btn-primary text-sm py-1.5">Add PDF</button>
                                                <button type="button" onclick="toggleContentForm('note-{{ $chapter->id }}')" class="btn-secondary text-sm py-1.5">Cancel</button>
                                            </div>
                                        </form>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-400 italic">No chapters yet</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="px-4 py-4 text-center">
                            <p class="text-gray-500">No subjects yet</p>
                            <a href="{{ route('teacher.curriculum.createSubject', [$course, $curriculum]) }}" class="text-sm text-blue-600 hover:text-blue-700 mt-1 inline-block">Add Subject</a>
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <p class="text-gray-500">No curriculum sections yet</p>
                <a href="{{ route('teacher.curriculum.createCurriculum', $course) }}" class="text-sm text-blue-600 hover:text-blue-700 mt-2 inline-block">Create First Section</a>
            </div>
        @endforelse
    </div>

    <script>
    function toggleContentForm(id) {
        const element = document.getElementById(id);
        if (element) {
            element.classList.toggle('hidden');
        }
    }
    </script>

    <!-- Load Subject Content -->
    @php
        $course->load(['curricula.subjects.contents', 'curricula.subjects.notes']);
    @endphp

    <!-- Enrolled Students -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Enrolled Students ({{ $students->count() }})</h3>
            <button onclick="alert('Export feature coming soon!')" class="text-sm text-blue-600 hover:text-blue-700">
                Export List
            </button>
        </div>

        @if($students->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($students as $enrollment)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold text-sm">{{ substr($enrollment->student->name, 0, 2) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $enrollment->student->name }}</p>
                                <p class="text-sm text-gray-500">{{ $enrollment->student->email }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <p class="text-sm text-gray-900">
                                    ₹{{ number_format($enrollment->fees_paid) }} / ₹{{ number_format($enrollment->fees_total) }}
                                </p>
                                <span class="badge {{ $enrollment->payment_status_badge_class }} text-xs">
                                    {{ ucfirst($enrollment->payment_status) }}
                                </span>
                            </div>
                            <span class="badge {{ $enrollment->status_badge_class }}">
                                {{ ucfirst($enrollment->enrollment_status) }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-8 text-center text-gray-500">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <p>No students enrolled yet</p>
            </div>
        @endif
    </div>

    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('teacher.courses') }}" class="btn-secondary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to My Courses
        </a>
    </div>
</div>
@endsection
