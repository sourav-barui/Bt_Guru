@extends('layouts.tenant')

@section('title', 'BTLive - ' . $liveClass->title)

@push('styles')
<style>
    /* Full screen mode - hide sidebar */
    body.fullscreen-mode .sidebar,
    body.fullscreen-mode .sidebar-nav,
    body.fullscreen-mode [class*="sidebar"],
    body.fullscreen-mode aside,
    body.fullscreen-mode nav:not(.bg-gradient-to-r) {
        display: none !important;
    }
    body.fullscreen-mode .main-content,
    body.fullscreen-mode .content-wrapper,
    body.fullscreen-mode main {
        margin-left: 0 !important;
        width: 100% !important;
    }
    body.fullscreen-mode {
        overflow: hidden;
    }
    #fullscreen-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 12px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s;
    }
    #fullscreen-btn:hover {
        background: rgba(0,0,0,0.9);
        transform: scale(1.1);
    }
</style>
@endpush

@section('page-content')
<!-- Fullscreen Toggle Button -->
<button id="fullscreen-btn" onclick="toggleFullScreen()" title="Toggle Full Screen">
    <svg id="fullscreen-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
    </svg>
</button>
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
                <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Recording
                </h3>
                
                <!-- Live Recording Status -->
                <div id="recording-status-container">
                    <div class="flex items-center gap-2 text-sm mb-2">
                        <span id="recording-indicator" class="w-2 h-2 rounded-full bg-gray-400"></span>
                        <span id="recording-status-text" class="text-gray-700">Ready to record</span>
                    </div>
                    
                    <!-- Recording Timer -->
                    <div id="recording-timer" class="hidden bg-gray-100 rounded-lg p-2 mb-2">
                        <div class="text-xs text-gray-500 uppercase tracking-wide">Recording Duration</div>
                        <div id="recording-time" class="text-2xl font-mono font-bold text-red-600">00:00:00</div>
                    </div>
                    
                    <!-- Recording Saved Message -->
                    <div id="recording-saved" class="hidden bg-green-50 border border-green-200 rounded-lg p-2 mb-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <div>
                                <div class="text-sm font-semibold text-green-800">Recording Saved!</div>
                                <div id="saved-recording-info" class="text-xs text-green-600"></div>
                            </div>
                        </div>
                        <!-- Recording Link Container -->
                        <div id="recording-link-container" class="hidden mt-2 pt-2 border-t border-green-200">
                            <a id="recording-link" href="#" target="_blank" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Watch Recording
                            </a>
                        </div>
                        <div id="recording-processing" class="hidden mt-2 pt-2 border-t border-green-200 text-xs text-green-700">
                            <span class="animate-pulse">⏳ Processing video... It will be available in curriculum shortly.</span>
                        </div>
                    </div>
                </div>
                
                @if($liveClass->btlive_recording_url)
                    <a href="{{ $liveClass->btlive_recording_url }}" target="_blank" class="mt-2 text-sm text-blue-600 hover:underline flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Watch Recording
                    </a>
                @endif
                
                <a href="{{ route('tenant.btlive.recordings.index', [$liveClass->course->tenant->slug ?? $liveClass->tenant->slug, $liveClass]) }}" target="_blank" class="mt-2 text-sm text-gray-600 hover:text-blue-600 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Manage Recordings
                </a>
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
function hideLoading() {
    const loading = document.getElementById('jitsi-loading');
    if (loading) loading.style.display = 'none';
}

// Hide Jitsi branding elements
function hideJitsiBranding() {
    // Try to access iframe and hide branding
    const iframe = document.getElementById('jitsiConferenceFrame0');
    if (iframe && iframe.contentWindow && iframe.contentWindow.document) {
        const jitsiDoc = iframe.contentWindow.document;
        // Hide watermarks
        const watermarks = jitsiDoc.querySelectorAll('.watermark, .leftwatermark, .rightwatermark');
        watermarks.forEach(el => el.style.display = 'none');
    }
}

// Toggle Full Screen Mode
function toggleFullScreen() {
    document.body.classList.toggle('fullscreen-mode');
    
    // Also toggle browser fullscreen API for video container
    const elem = document.documentElement;
    if (!document.fullscreenElement) {
        elem.requestFullscreen().catch(err => {
            console.log('Fullscreen error:', err);
        });
    } else {
        document.exitFullscreen();
    }
    
    // Change icon
    const icon = document.getElementById('fullscreen-icon');
    if (document.body.classList.contains('fullscreen-mode')) {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>';
    } else {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>';
    }
}

// Auto-enter fullscreen on load
setTimeout(() => {
    document.body.classList.add('fullscreen-mode');
    // Request browser fullscreen
    const elem = document.documentElement;
    if (elem.requestFullscreen) {
        elem.requestFullscreen().catch(e => console.log('Fullscreen:', e));
    } else if (elem.webkitRequestFullscreen) {
        elem.webkitRequestFullscreen();
    } else if (elem.msRequestFullscreen) {
        elem.msRequestFullscreen();
    }
}, 1000);

// Auto-join the conference when ready
api.addEventListener('videoConferenceJoined', () => {
    hideLoading();
    // Lock to landscape on mobile
    if (screen.orientation && screen.orientation.lock) {
        screen.orientation.lock('landscape').catch(e => console.log('Orientation:', e));
    }
});

// Try to auto-execute join
api.executeCommand('toggleAudio', []);
api.executeCommand('toggleVideo', []);

// Run branding hide after load
setTimeout(hideJitsiBranding, 3000);
setTimeout(hideJitsiBranding, 6000);

api.addEventListener('videoConferenceJoined', hideLoading);
api.addEventListener('ready', hideLoading);
api.addEventListener('prejoinScreenLoaded', hideLoading);

// Also hide after 5 seconds as fallback
setTimeout(hideLoading, 5000);

// Track participant joins
api.addEventListener('participantJoined', (participant) => {
    updateAttendance();
});

// Track participant leaves
api.addEventListener('participantLeft', (participant) => {
    updateAttendance();
});

// Screen sharing - auto toggle video
api.addEventListener('screenSharingStatusChanged', (status) => {
    if (status.on) {
        // Screen sharing started - turn off video
        api.executeCommand('toggleVideo', false);
    } else {
        // Screen sharing stopped - turn on video
        api.executeCommand('toggleVideo', true);
    }
});

// Recording status with live updates
let recordingStartTime = null;
let recordingTimerInterval = null;
let isRecording = false;

function updateRecordingDisplay(status) {
    const indicator = document.getElementById('recording-indicator');
    const statusText = document.getElementById('recording-status-text');
    const timer = document.getElementById('recording-timer');
    const timeDisplay = document.getElementById('recording-time');
    const savedMessage = document.getElementById('recording-saved');
    
    if (status === 'on' || status.on === true) {
        // Recording started
        isRecording = true;
        recordingStartTime = Date.now();
        
        indicator.className = 'w-2 h-2 rounded-full bg-red-500 animate-pulse';
        statusText.textContent = 'Recording in progress...';
        statusText.className = 'text-red-600 font-semibold';
        timer.classList.remove('hidden');
        savedMessage.classList.add('hidden');
        
        // Start timer
        if (recordingTimerInterval) clearInterval(recordingTimerInterval);
        recordingTimerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - recordingStartTime) / 1000);
            const hours = Math.floor(elapsed / 3600);
            const minutes = Math.floor((elapsed % 3600) / 60);
            const seconds = elapsed % 60;
            timeDisplay.textContent = 
                String(hours).padStart(2, '0') + ':' + 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');
        }, 1000);
        
    } else if (status === 'off' || status.on === false) {
        // Recording stopped
        isRecording = false;
        
        if (recordingTimerInterval) {
            clearInterval(recordingTimerInterval);
            recordingTimerInterval = null;
        }
        
        indicator.className = 'w-2 h-2 rounded-full bg-green-500';
        statusText.textContent = 'Recording stopped';
        statusText.className = 'text-green-600 font-semibold';
        timer.classList.add('hidden');
        savedMessage.classList.remove('hidden');
        
        // Calculate final duration
        let durationText = '';
        if (recordingStartTime) {
            const duration = Math.floor((Date.now() - recordingStartTime) / 1000);
            const minutes = Math.floor(duration / 60);
            const seconds = duration % 60;
            durationText = minutes > 0 ? 
                `${minutes}m ${seconds}s` : `${seconds}s`;
            document.getElementById('saved-recording-info').textContent = 
                `Duration: ${durationText} • Saved to server • Processing...`;
        }
        
        // Notify server that recording ended
        notifyRecordingEnded(durationText);
    }
}

function notifyRecordingEnded(duration) {
    // Show processing state
    document.getElementById('recording-processing').classList.remove('hidden');
    
    fetch('{{ route("btlive.recording_webhook", $liveClass) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            event_type: 'recording.finished',
            recording_id: 'btlive_' + '{{ $liveClass->id }}',
            live_class_id: '{{ $liveClass->id }}',
            duration: duration,
            ended_at: new Date().toISOString()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.recording_url) {
            // Show recording link
            document.getElementById('recording-processing').classList.add('hidden');
            document.getElementById('recording-link-container').classList.remove('hidden');
            document.getElementById('recording-link').href = data.recording_url;
            
            // Update saved info
            document.getElementById('saved-recording-info').textContent = 
                `Duration: ${duration} • Video ready to view`;
            
            // Auto-save to curriculum
            if (data.auto_save) {
                console.log('Recording auto-saved to curriculum');
            }
        }
    })
    .catch(e => console.log('Recording webhook failed:', e));
}

api.addEventListener('recordingStatusChanged', (status) => {
    console.log('Recording status changed:', status);
    updateRecordingDisplay(status);
});

// Also listen for explicit start/stop events
api.addEventListener('recordingStarted', (data) => {
    console.log('Recording started:', data);
    updateRecordingDisplay('on');
});

api.addEventListener('recordingStopped', (data) => {
    console.log('Recording stopped:', data);
    updateRecordingDisplay('off');
});

// End meeting
function endMeeting() {
    if (!confirm('Are you sure you want to end this class for all participants?')) {
        return;
    }
    
    // Stop recording if active
    if (isRecording) {
        api.executeCommand('stopRecording', 'file');
    }
    
    // First save recording info, then end meeting
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
            // Small delay to allow recording webhook to process
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2000);
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
