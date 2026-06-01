@extends('layouts.student_mobile')

@section('title', 'BTLive - ' . $liveClass->title)

@push('styles')
<style>
    /* Auto-hide bottom action bar */
    #action-bar {
        transition: transform 0.3s ease;
    }
    #action-bar.hidden-bar {
        transform: translateY(100%);
    }
    #action-bar:hover, 
    #action-bar.show-bar {
        transform: translateY(0);
    }
    /* Fullscreen button */
    #fullscreen-btn {
        position: fixed;
        bottom: 80px;
        right: 15px;
        z-index: 1000;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 10px;
        border-radius: 50%;
        cursor: pointer;
        transition: all 0.3s;
    }
    #fullscreen-btn:hover {
        background: rgba(0,0,0,0.9);
        transform: scale(1.1);
    }
    /* Toggle bar button */
    #toggle-bar-btn {
        position: fixed;
        bottom: 15px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1001;
        background: rgba(0,0,0,0.5);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.3s;
    }
</style>
@endpush

@section('mobile-content')
<div class="h-screen flex flex-col">
    <!-- Header -->
    <div class="bg-gradient-to-r from-red-600 to-red-800 text-white px-4 py-3 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-sm">{{ Str::limit($liveClass->title, 25) }}</h1>
                <p class="text-xs text-white/70">{{ $liveClass->course->title }}</p>
            </div>
        </div>
        
        <!-- Live Indicator -->
        <div class="flex items-center gap-1.5 bg-red-500/50 px-2 py-1 rounded-full">
            <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
            <span class="text-xs font-semibold">LIVE</span>
        </div>
    </div>
    
    <!-- Notice -->
    <div class="bg-yellow-50 border-b border-yellow-200 px-4 py-2 text-center">
        <p class="text-xs text-yellow-800">
            <span class="font-semibold">Classroom Rules:</span> 
            Video/Audio disabled • Chat enabled • Raise hand to speak
        </p>
    </div>
    
    <!-- Jitsi Container -->
    <div class="flex-1 relative bg-gray-900">
        <div id="jitsi-container" class="absolute inset-0"></div>
        
        <!-- Loading State -->
        <div id="jitsi-loading" class="absolute inset-0 flex items-center justify-center bg-gray-900">
            <div class="text-center px-4">
                <div class="w-12 h-12 border-4 border-red-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
                <p class="text-white font-semibold text-sm">Joining BTLive...</p>
                <p class="text-gray-400 text-xs mt-1">Please wait while we connect you</p>
            </div>
        </div>
        
        <!-- Connection Error -->
        <div id="jitsi-error" class="hidden absolute inset-0 flex items-center justify-center bg-gray-900">
            <div class="text-center px-4">
                <svg class="w-12 h-12 text-red-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-white font-semibold text-sm">Connection failed</p>
                <p class="text-gray-400 text-xs mt-1 mb-3">Please check your internet connection</p>
                <button onclick="location.reload()" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Retry
                </button>
            </div>
        </div>
    </div>
    
    <!-- Fullscreen Toggle Button -->
<button id="fullscreen-btn" onclick="toggleFullScreen()" title="Toggle Full Screen">
    <svg id="fullscreen-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
    </svg>
</button>

<!-- Toggle Bar Button -->
<button id="toggle-bar-btn" onclick="toggleActionBar()">Hide Bar ↑</button>

<!-- Quick Actions Bar -->
    <div id="action-bar" class="bg-white border-t border-gray-200 px-4 py-3 flex items-center justify-around shrink-0">
        <button onclick="toggleChat()" class="flex flex-col items-center gap-1 text-gray-600">
            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                </svg>
            </div>
            <span class="text-xs">Chat</span>
        </button>
        
        <button onclick="raiseHand()" class="flex flex-col items-center gap-1 text-gray-600">
            <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center" id="hand-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path>
                </svg>
            </div>
            <span class="text-xs">Raise Hand</span>
        </button>
        
        <button onclick="leaveClass()" class="flex flex-col items-center gap-1 text-red-600">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28A1 1 0 0020.28 15l-3.734-2.45a1 1 0 00-1.24.083l-1.38 1.1a1 1 0 01-1.28.003l-1.953-1.553a1 1 0 01-.134-1.476l2.057-2.57a1 1 0 011.133-.322l1.95.585a1 1 0 00.787-.053l2.65-1.06a1 1 0 00.682-.945V5a2 2 0 00-2-2H5z"></path>
                </svg>
            </div>
            <span class="text-xs">Leave</span>
        </button>
    </div>
</div>

<!-- Raise Hand Toast -->
<div id="hand-toast" class="hidden fixed top-20 left-1/2 transform -translate-x-1/2 bg-yellow-500 text-white px-4 py-2 rounded-full shadow-lg text-sm font-medium z-50">
    ✋ Hand raised! Teacher will be notified.
</div>

@push('scripts')
<script src='https://{{ config('btlive.jitsi_domain', 'meet.jit.si') }}/external_api.js'></script>
<script>
const jitsiConfig = @json($jitsiConfig);
const jwt = @json($jwt);
let handRaised = false;

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

let api;
try {
    api = new JitsiMeetExternalAPI(domain, options);
} catch (e) {
    console.error('Jitsi init failed:', e);
    document.getElementById('jitsi-loading').classList.add('hidden');
    document.getElementById('jitsi-error').classList.remove('hidden');
}

if (api) {
    // Hide loading when ready
    function hideLoading() {
        const loading = document.getElementById('jitsi-loading');
        if (loading) loading.style.display = 'none';
    }
    
    // Hide Jitsi branding
    function hideJitsiBranding() {
        const iframe = document.getElementById('jitsiConferenceFrame0');
        if (iframe && iframe.contentWindow && iframe.contentWindow.document) {
            const jitsiDoc = iframe.contentWindow.document;
            const watermarks = jitsiDoc.querySelectorAll('.watermark, .leftwatermark, .rightwatermark');
            watermarks.forEach(el => el.style.display = 'none');
        }
    }
    setTimeout(hideJitsiBranding, 3000);
    setTimeout(hideJitsiBranding, 6000);
    
    // Auto-hide action bar after 5 seconds
    let barTimeout;
    function autoHideBar() {
        barTimeout = setTimeout(() => {
            document.getElementById('action-bar').classList.add('hidden-bar');
            document.getElementById('toggle-bar-btn').textContent = 'Show Bar ↓';
        }, 5000);
    }
    
    // Toggle action bar
    window.toggleActionBar = function() {
        const bar = document.getElementById('action-bar');
        const btn = document.getElementById('toggle-bar-btn');
        bar.classList.toggle('hidden-bar');
        if (bar.classList.contains('hidden-bar')) {
            btn.textContent = 'Show Bar ↓';
            clearTimeout(barTimeout);
        } else {
            btn.textContent = 'Hide Bar ↑';
            autoHideBar();
        }
    };
    
    // Show bar on hover of bottom area
    document.addEventListener('mousemove', (e) => {
        if (e.clientY > window.innerHeight - 50) {
            document.getElementById('action-bar').classList.remove('hidden-bar');
        }
    });
    
    // Fullscreen toggle
    window.toggleFullScreen = function() {
        const elem = document.documentElement;
        if (!document.fullscreenElement) {
            elem.requestFullscreen().catch(err => console.log(err));
        } else {
            document.exitFullscreen();
        }
    };
    
    // Start auto-hide
    autoHideBar();
    
    api.addEventListener('videoConferenceJoined', hideLoading);
    api.addEventListener('ready', hideLoading);
    api.addEventListener('prejoinScreenLoaded', hideLoading);
    
    // Also hide after 5 seconds as fallback
    setTimeout(hideLoading, 5000);
    
    // Handle errors
    api.addEventListener('errorOccurred', (error) => {
        console.error('Jitsi error:', error);
        if (error.error === 'conference.authenticationRequired') {
            document.getElementById('jitsi-loading').classList.add('hidden');
            document.getElementById('jitsi-error').classList.remove('hidden');
        }
    });
    
    // Handle kick
    api.addEventListener('participantKickedOut', (event) => {
        alert('You have been removed from the class by the teacher.');
        window.location.href = '{{ route("student.live_classes.index") }}';
    });
}

// Toggle chat
function toggleChat() {
    if (api) {
        api.executeCommand('toggleChat');
    }
}

// Raise hand
function raiseHand() {
    handRaised = !handRaised;
    
    if (api) {
        api.executeCommand('toggleRaiseHand');
    }
    
    const btn = document.getElementById('hand-btn');
    const toast = document.getElementById('hand-toast');
    
    if (handRaised) {
        btn.classList.add('bg-yellow-400', 'text-white');
        btn.classList.remove('bg-gray-100');
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 3000);
    } else {
        btn.classList.remove('bg-yellow-400', 'text-white');
        btn.classList.add('bg-gray-100');
    }
}

// Leave class
function leaveClass() {
    if (!confirm('Leave this class?')) {
        return;
    }
    
    // Record leave attendance
    navigator.sendBeacon ? 
        navigator.sendBeacon('/api/btlive/{{ $liveClass->id }}/leave') :
        fetch('/api/btlive/{{ $liveClass->id }}/leave', {method: 'POST', keepalive: true});
    
    if (api) {
        api.executeCommand('hangup');
    }
    
    window.location.href = '{{ route("student.live_classes.index") }}';
}

// Handle beforeunload - record attendance
window.addEventListener('beforeunload', () => {
    fetch('/api/btlive/{{ $liveClass->id }}/leave', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        keepalive: true,
    });
});

// Keep screen awake (wake lock API if available)
if ('wakeLock' in navigator) {
    navigator.wakeLock.request('screen').catch(err => {
        console.log('Wake lock failed:', err);
    });
}
</script>
@endpush

@push('styles')
<style>
#jitsi-container iframe {
    width: 100% !important;
    height: 100% !important;
    border: none;
}
/* Hide Jitsi watermark for cleaner student view */
#jitsi-container .watermark {
    display: none !important;
}
</style>
@endpush
@endsection
