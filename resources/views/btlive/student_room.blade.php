@extends('layouts.tenant')

@section('title', 'BTLive - ' . $liveClass->title)

@push('styles')
<style>
    body.fullscreen-mode .sidebar { display: none !important; }
    body.fullscreen-mode .main-content { margin-left: 0 !important; width: 100% !important; }
    #fullscreen-btn { position: fixed; bottom: 20px; right: 20px; z-index: 1000; background: rgba(0,0,0,0.7); color: white; padding: 12px; border-radius: 50%; cursor: pointer; }
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
        <div id="jitsi-container" class="absolute inset-0"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src='https://meet.jit.si/external_api.js'></script>
<script>
    const domain = '{{ $jitsiConfig['domain'] }}';
    const roomName = '{{ $jitsiConfig['roomName'] }}';
    const jwt = '{{ $jwt }}';
    
    const api = new JitsiMeetExternalAPI(domain, {
        roomName: roomName,
        jwt: jwt,
        parentNode: document.getElementById('jitsi-container'),
    });
    
    function leaveMeeting() {
        api.executeCommand('hangup');
        window.location.href = '{{ route('student.live_classes.index') }}';
    }
</script>
@endpush
