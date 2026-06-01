@php
    $platformColors = [
        'google_meet' => ['bg' => 'bg-green-100', 'text' => 'text-green-700', 'icon' => 'G'],
        'zoom'        => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'Z'],
        'ms_teams'    => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'T'],
        'jitsi'       => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => 'J'],
        'other'       => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'O'],
    ];
    $pStyle = $platformColors[$lc->platform] ?? $platformColors['other'];
    
    // BTLive override
    if($lc->is_btlive) {
        $pStyle = ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => '🔴'];
    }
    
    // Get student join URL
    $joinUrl = $lc->is_btlive ? route('student.btlive.join', $lc) : $lc->secure_meeting_url;
    
    $statusConfig = [
        'live' => ['badge' => 'bg-red-100 text-red-700', 'ring' => 'ring-red-400', 'btn' => 'tb-btn-primary bg-gradient-to-r from-red-500 to-red-700 hover:from-red-600 hover:to-red-800'],
        'upcoming' => ['badge' => 'bg-indigo-100 text-indigo-700', 'ring' => 'ring-indigo-400', 'btn' => 'tb-btn-primary'],
        'past' => ['badge' => 'bg-gray-100 text-gray-600', 'ring' => 'ring-gray-300', 'btn' => 'tb-btn-secondary'],
    ];
    $cfg = $statusConfig[$mode];
@endphp

<div class="tb-course-card mb-4 {{ $mode === 'live' ? 'ring-2 ring-red-400 shadow-lg' : '' }}">
    {{-- Header with platform icon --}}
    <div class="flex items-start gap-4 p-4">
        <div class="w-14 h-14 rounded-xl {{ $pStyle['bg'] }} flex items-center justify-center flex-shrink-0 shadow-sm">
            <span class="text-lg font-bold {{ $pStyle['text'] }}">{{ $pStyle['icon'] }}</span>
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <h3 class="font-bold text-gray-900 text-base truncate">{{ $lc->title }}</h3>
                @if($mode === 'live')
                    <span class="px-2 py-0.5 rounded-full bg-red-100 text-red-700 text-xs font-bold animate-pulse">LIVE</span>
                @endif
            </div>
            <p class="text-sm text-gray-500">{{ $lc->course->title }}</p>
            <div class="flex items-center gap-3 mt-2">
                <span class="px-2 py-1 rounded-lg {{ $cfg['badge'] }} text-xs font-semibold">
                    {{ $lc->platform_label }}
                </span>
                @if($lc->is_public)
                    <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-0.5 rounded-full">🌍 Public</span>
                @endif
                @if($lc->recurrence !== 'none')
                    <span class="text-xs text-indigo-600 font-medium">🔁 {{ ucfirst($lc->recurrence) }}</span>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Divider --}}
    <div class="border-t border-gray-100"></div>
    
    {{-- Time & Date Info --}}
    <div class="p-4 bg-gray-50/50">
        <div class="grid grid-cols-2 gap-3 mb-3">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700">{{ $lc->scheduled_at->format('D, d M') }}</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-700">{{ $lc->scheduled_at->format('h:i A') }}</span>
            </div>
        </div>
        
        @if($lc->duration_minutes)
            <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Duration: {{ $lc->duration_minutes }} minutes
            </div>
        @endif
        
        {{-- Meeting Details --}}
        @if($lc->meeting_id || $lc->meeting_password)
            <div class="bg-white rounded-lg p-3 border border-gray-200 mb-3">
                <div class="flex gap-4">
                    @if($lc->meeting_id)
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Meeting ID</p>
                            <p class="text-sm font-mono font-semibold text-gray-800">{{ $lc->meeting_id }}</p>
                        </div>
                    @endif
                    @if($lc->meeting_password)
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Passcode</p>
                            <p class="text-sm font-mono font-semibold text-gray-800">{{ $lc->meeting_password }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        
        {{-- Join/Watch Button --}}
        @if($mode === 'live')
            <a href="{{ $joinUrl }}" target="_blank" class="tb-btn-primary w-full justify-center bg-gradient-to-r from-red-500 to-red-700 hover:from-red-600 hover:to-red-800 border-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                {{ $lc->is_btlive ? '🔴 Join BTLive' : 'Join Live Now' }}
            </a>
        @elseif($mode === 'upcoming')
            <div class="flex items-center justify-center gap-2 py-3 bg-indigo-50 rounded-xl text-indigo-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Starts {{ $lc->scheduled_at->diffForHumans() }}</span>
            </div>
        @elseif($lc->video_url)
            <a href="{{ $lc->video_url }}" target="_blank" class="tb-btn-primary w-full justify-center bg-gradient-to-r from-blue-500 to-blue-700 hover:from-blue-600 hover:to-blue-800 border-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Watch Recording
            </a>
        @else
            <div class="flex items-center justify-center gap-2 py-3 bg-gray-100 rounded-xl text-gray-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Class Completed</span>
            </div>
        @endif
    </div>
</div>
