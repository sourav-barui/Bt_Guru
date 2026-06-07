@extends('layouts.tenant')

@section('title', 'BTLive - ' . $liveClass->title)

@push('styles')
<style>
    body.fullscreen-mode .sidebar { display: none !important; }
    body.fullscreen-mode .main-content { margin-left: 0 !important; width: 100% !important; }
    #fullscreen-btn { position: fixed; bottom: 20px; right: 20px; z-index: 1000; background: rgba(0,0,0,0.7); color: white; padding: 12px; border-radius: 50%; cursor: pointer; }
    #jitsi-loading { display: flex; align-items: center; justify-content: center; height: 100%; color: white; font-size: 1.2rem; }
    #jitsi-error { display: none; padding: 2rem; text-align: center; color: white; }
    #jitsi-error a { color: #60a5fa; text-decoration: underline; }
</style>
@endpush

@section('page-content')
<div class="h-screen flex flex-col -m-6">
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-4 py-3 flex items-center justify-between">
        <div>
            <h1 class="font-bold text-lg">{{ $liveClass->title }}</h1>
            <p class="text-sm text-white/70">{{ $liveClass->course?->title ?? 'Course' }}</p>
        </div>
        <button onclick="leaveMeeting()" class="bg-white text-red-700 px-4 py-2 rounded-lg font-semibold">Leave Class</button>
    </div>
    <div class="flex-1 relative bg-gray-900">
        <div id="jitsi-loading">
            <div class="flex flex-col items-center gap-4">
                <svg class="animate-spin h-8 w-8 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p>Loading classroom...</p>
            </div>
        </div>
        <div id="jitsi-error">
            <h3 class="text-xl font-bold mb-2">Unable to load classroom</h3>
            <p class="mb-4">There was a problem joining the live class.</p>
            <p class="text-sm text-gray-400 mb-4" id="error-details"></p>
            <a href="{{ route('student.live_classes.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Back to Live Classes</a>
        </div>
        <div id="jitsi-container" class="absolute inset-0" style="display: none;"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://{{ env('BTLIVE_JITSI_DOMAIN', 'meet.btguru.tech') }}/external_api.js' onload="initJitsi()" onerror="handleJitsiLoadError()"></script>
<script>
    const jitsiConfig = @json($jitsiConfig);
    const jwt = '{{ $jwt }}';
    let api = null;

    function initJitsi() {
        console.log('Jitsi script loaded, initializing...');
        console.log('Config:', jitsiConfig);
        
        if (typeof JitsiMeetExternalAPI === 'undefined') {
            showError('Jitsi API not available. Please refresh the page.');
            return;
        }

        try {
            const container = document.getElementById('jitsi-container');
            const loading = document.getElementById('jitsi-loading');
            
            api = new JitsiMeetExternalAPI(jitsiConfig.domain, {
                roomName: jitsiConfig.roomName,
                jwt: jwt,
                parentNode: container,
                width: '100%',
                height: '100%',
            });

            api.addEventListener('videoConferenceJoined', () => {
                console.log('Conference joined successfully');
                loading.style.display = 'none';
                container.style.display = 'block';
            });

            api.addEventListener('videoConferenceLeft', () => {
                console.log('Conference left');
                leaveMeeting();
            });

            api.addEventListener('readyToClose', () => {
                console.log('Ready to close');
                leaveMeeting();
            });

            // Timeout fallback - show container after 5 seconds even if event doesn't fire
            setTimeout(() => {
                if (loading.style.display !== 'none') {
                    loading.style.display = 'none';
                    container.style.display = 'block';
                }
            }, 5000);

        } catch (error) {
            console.error('Jitsi initialization error:', error);
            showError('Failed to initialize classroom: ' + error.message);
        }
    }

    function handleJitsiLoadError() {
        console.error('Failed to load Jitsi script');
        showError('Could not load video conference library. Check your internet connection.');
    }

    function showError(message) {
        document.getElementById('jitsi-loading').style.display = 'none';
        document.getElementById('jitsi-container').style.display = 'none';
        document.getElementById('jitsi-error').style.display = 'block';
        document.getElementById('error-details').textContent = message;
    }

    function leaveMeeting() {
        if (api) {
            try {
                api.dispose();
            } catch (e) {
                console.log('Error disposing API:', e);
            }
        }
        window.location.href = '{{ route('student.live_classes.index') }}';
    }
</script>
@endpush
