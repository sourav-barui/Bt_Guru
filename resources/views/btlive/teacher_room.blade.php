@extends('layouts.tenant')

@section('title', 'BTLive - ' . $liveClass->title)
@section('page-title', 'BTLive Classroom')

@section('page-content')
<div class="h-screen flex flex-col -m-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white px-4 py-3 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-lg">{{ $liveClass->title }}</h1>
                <p class="text-sm text-white/70">{{ $liveClass->course->title }} • BTLive Classroom</p>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <!-- Live Indicator -->
            <div class="flex items-center gap-2 bg-red-500/50 px-3 py-1.5 rounded-full">
                <span class="w-2 h-2 bg-white rounded-full animate-pulse"></span>
                <span class="text-sm font-semibold">LIVE</span>
            </div>
            
            <!-- End Class Button -->
            <button onclick="endMeeting()" class="bg-white text-red-700 px-4 py-2 rounded-lg font-semibold hover:bg-red-50 transition flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28A1 1 0 0020.28 15l-3.734-2.45a1 1 0 00-1.24.083l-1.38 1.1a1 1 0 01-1.28.003l-1.953-1.553a1 1 0 01-.134-1.476l2.057-2.57a1 1 0 011.133-.322l1.95.585a1 1 0 00.787-.053l2.65-1.06a1 1 0 00.682-.945V5a2 2 0 00-2-2H5z"></path>
                </svg>
                End Class
            </button>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex-1 flex overflow-hidden">
        <!-- Jitsi Container -->
        <div class="flex-1 relative bg-gray-900">
            <div id="jitsi-container" class="absolute inset-0"></div>
            
            <!-- Loading State -->
            <div id="jitsi-loading" class="absolute inset-0 flex items-center justify-center bg-gray-900">
                <div class="text-center">
                    <div class="w-16 h-16 border-4 border-red-600 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-white font-semibold">Loading BTLive Classroom...</p>
                    <p class="text-gray-400 text-sm mt-2">Room: {{ $liveClass->btlive_room_name }}</p>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="w-80 bg-white border-l border-gray-200 flex flex-col shrink-0">
            <!-- Attendance Stats -->
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Attendance
                </h3>
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-blue-600" id="attendance-count">{{ $attendanceStats['total_present'] }}</p>
                        <p class="text-xs text-blue-600/70">Present</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-green-600">{{ $attendanceStats['attendance_percentage'] }}%</p>
                        <p class="text-xs text-green-600/70">Rate</p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                    {{ $attendanceStats['total_enrolled'] }} total enrolled • Avg {{ $attendanceStats['average_duration_minutes'] }} min
                </p>
            </div>
            
            <!-- Quick Actions -->
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-3">Controls</h3>
                <div class="space-y-2">
                    <button onclick="muteAllStudents()" class="w-full flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm text-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2 2m2-2l2 2"></path>
                        </svg>
                        Mute All Students
                    </button>
                    <button onclick="toggleLobby()" class="w-full flex items-center gap-2 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-sm text-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        {{ $liveClass->btlive_lobby_enabled ? 'Disable' : 'Enable' }} Lobby
                    </button>
                    <a href="{{ route('btlive.attendance', $liveClass) }}" target="_blank" class="w-full flex items-center gap-2 px-3 py-2 bg-blue-50 hover:bg-blue-100 rounded-lg text-sm text-blue-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Full Report
                    </a>
                </div>
            </div>
            
            <!-- Recording Status -->
            <div class="p-4 border-b border-gray-200">
                <h3 class="font-semibold text-gray-900 mb-3">Recording</h3>
                <div class="flex items-center gap-2 text-sm">
                    <span class="w-2 h-2 rounded-full {{ $liveClass->btlive_recording_status === 'recording' ? 'bg-red-500 animate-pulse' : 'bg-gray-400' }}"></span>
                    <span class="text-gray-700">
                        @switch($liveClass->btlive_recording_status)
                            @case('recording') Recording in progress... @break
                            @case('processing') Processing... @break
                            @case('completed') Recording available @break
                            @default Ready to record
                        @endswitch
                    </span>
                </div>
                @if($liveClass->btlive_recording_url)
                    <a href="{{ $liveClass->btlive_recording_url }}" target="_blank" class="mt-2 text-sm text-blue-600 hover:underline flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Watch Recording
                    </a>
                @endif
            </div>
            
            <!-- Class Info -->
            <div class="p-4 flex-1 overflow-y-auto">
                <h3 class="font-semibold text-gray-900 mb-3">Class Info</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Started at</span>
                        <span class="text-gray-900">{{ $liveClass->btlive_started_at?->format('h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Duration</span>
                        <span class="text-gray-900">{{ $liveClass->duration_minutes }} min</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Room</span>
                        <span class="text-gray-900 font-mono text-xs">{{ Str::limit($liveClass->btlive_room_name, 20) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src='https://{{ config('btlive.jitsi_domain', 'meet.jit.si') }}/external_api.js'></script>
<script>
const jitsiConfig = @json($jitsiConfig);
const jwt = @json($jwt);

// Initialize Jitsi
const domain = '{{ config('btlive.jitsi_domain', 'meet.jit.si') }}';
const options = {
    roomName: jitsiConfig.roomName,
    parentNode: document.getElementById('jitsi-container'),
    configOverwrite: jitsiConfig.configOverwrite,
    interfaceConfigOverwrite: jitsiConfig.interfaceConfigOverwrite,
    userInfo: jitsiConfig.userInfo,
};

// Only pass JWT if required
if (jwt && '{{ config('btlive.require_jwt', true) }}' === '1') {
    options.jwt = jwt;
}

const api = new JitsiMeetExternalAPI(domain, options);

// Hide loading when ready
api.addEventListener('videoConferenceJoined', () => {
    document.getElementById('jitsi-loading').style.display = 'none';
});

// Track participant joins
api.addEventListener('participantJoined', (participant) => {
    updateAttendance();
});

// Track participant leaves
api.addEventListener('participantLeft', (participant) => {
    updateAttendance();
});

// Recording status
api.addEventListener('recordingStatusChanged', (status) => {
    console.log('Recording status:', status);
});

// End meeting
function endMeeting() {
    if (!confirm('Are you sure you want to end this class for all participants?')) {
        return;
    }
    
    fetch('{{ route("btlive.end_meeting", $liveClass) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            api.executeCommand('hangup');
            window.location.href = data.redirect;
        }
    });
}

// Mute all students
function muteAllStudents() {
    api.executeCommand('muteEveryone', 'audio');
    alert('All students have been muted.');
}

// Toggle lobby
function toggleLobby() {
    // This would require server-side toggle
    alert('Lobby toggle requires API integration.');
}

// Update attendance stats
function updateAttendance() {
    fetch('{{ route("btlive.live_stats", $liveClass) }}')
        .then(r => r.json())
        .then(data => {
            document.getElementById('attendance-count').textContent = data.stats.total_present;
        });
}

// Poll attendance every 30 seconds
setInterval(updateAttendance, 30000);

// Handle beforeunload
window.addEventListener('beforeunload', (e) => {
    // Don't allow closing without confirmation if live
    e.preventDefault();
    e.returnValue = '';
});
</script>
@endpush

@push('styles')
<style>
#jitsi-container iframe {
    width: 100% !important;
    height: 100% !important;
    border: none;
}
</style>
@endpush
@endsection
