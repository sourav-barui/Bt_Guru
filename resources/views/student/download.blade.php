@php
    $tenant = app('current_tenant');
    $coachingName = $tenant?->coaching_name ?? 'BT Guru';
    $pwaIcon = $tenant?->pwa_icon ?? $tenant?->logo ?? null;
    $apkUrl = $tenant ? "/downloads/{$tenant->subdomain}/student.apk" : '/downloads/btguru-student.apk';
@endphp
@extends('layouts.student_mobile')

@section('title', 'Download App')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-600 to-purple-900 text-white">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            @if($pwaIcon)
                <img src="{{ Storage::url($pwaIcon) }}" alt="{{ $coachingName }}" class="w-20 h-20 mx-auto rounded-2xl shadow-lg mb-4 bg-white p-2">
            @else
                <div class="w-20 h-20 mx-auto rounded-2xl bg-white/20 flex items-center justify-center mb-4">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            @endif
            <h1 class="text-2xl font-bold mb-2">{{ $coachingName }}</h1>
            <p class="text-purple-200">Download the app for the best experience</p>
        </div>

        <!-- Android Download Card -->
        <div class="bg-white/10 backdrop-blur rounded-2xl p-6 mb-4">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.523 15.3414c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993.0001.5511-.4482.9997-.9993.9997m-11.046 0c-.5511 0-.9993-.4486-.9993-.9997s.4482-.9993.9993-.9993c.5511 0 .9993.4482.9993.9993 0 .5511-.4482.9997-.9993.9997m11.4045-6.02l1.9973-3.4592a.416.416 0 00-.1521-.5676.416.416 0 00-.5676.1521l-2.0225 3.503C15.5902 8.4794 13.8538 8.138 12 8.138c-1.8538 0-3.5902.3414-5.1368.9489L4.8407 5.5837a.416.416 0 00-.5676-.1521.416.416 0 00-.1521.5676l1.9973 3.4592C2.6889 11.1867.3432 14.6589.3432 18.6617h23.3136c0-4.0028-2.3457-7.475-5.7754-9.3403"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-lg">Android App</h3>
                    <p class="text-purple-200 text-sm">APK download for Android phones</p>
                </div>
            </div>
            
            <a href="{{ $apkUrl }}" download
               class="block w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-4 rounded-xl text-center transition-all transform active:scale-95">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download {{ $coachingName }} App
                </span>
            </a>
            
            <div class="mt-4 text-sm text-purple-200 space-y-2">
                <p class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Works offline after install
                </p>
                <p class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Push notifications for exams & classes
                </p>
            </div>
        </div>

        <!-- iOS / PWA Card -->
        <div class="bg-white/10 backdrop-blur rounded-2xl p-6 mb-4">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center">
                    <svg class="w-7 h-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.21-1.98 1.08-3.11-1.05.05-2.31.7-3.06 1.66-.68.84-1.21 2.01-1.07 3.14 1.15.09 2.33-.66 3.05-1.69z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-semibold text-lg">iPhone & iPad</h3>
                    <p class="text-purple-200 text-sm">Add to Home Screen</p>
                </div>
            </div>
            
            <button onclick="showIosInstructions()"
                    class="block w-full bg-gray-700 hover:bg-gray-600 text-white font-semibold py-4 rounded-xl text-center transition-all">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    How to Install
                </span>
            </button>
        </div>

        <!-- Quick Install Option -->
        <div class="bg-white rounded-2xl p-6 text-gray-800">
            <h3 class="font-semibold text-lg mb-2 text-purple-700">Quick Install (Recommended)</h3>
            <p class="text-gray-600 text-sm mb-4">No download needed! Add this website to your home screen for instant access.</p>
            
            <button id="quickInstallBtn" onclick="installPWA()"
                    class="block w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-4 rounded-xl text-center transition-all">
                <span class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add to Home Screen
                </span>
            </button>
        </div>

        <!-- Install Instructions Modal -->
        <div id="iosModal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 text-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-lg">Install on iPhone/iPad</h3>
                    <button onclick="hideIosModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-bold">1</span>
                        <p>Tap the <strong>Share</strong> button <span class="text-2xl">⎋</span> at the bottom of Safari</p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-bold">2</span>
                        <p>Scroll down and tap <strong>"Add to Home Screen"</strong></p>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center font-bold">3</span>
                        <p>Tap <strong>"Add"</strong> in the top right</p>
                    </div>
                </div>
                <button onclick="hideIosModal()" class="mt-6 w-full bg-purple-600 text-white py-3 rounded-xl font-semibold">Got it!</button>
            </div>
        </div>
    </div>
</div>

<script>
    let deferredPrompt = null;

    // Capture install prompt
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
    });

    function installPWA() {
        if (deferredPrompt) {
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    showToast('App installed successfully!');
                }
                deferredPrompt = null;
            });
        } else if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
            showIosInstructions();
        } else {
            showToast('Tap menu (3 dots) → "Add to Home screen"');
        }
    }

    function showIosInstructions() {
        document.getElementById('iosModal').classList.remove('hidden');
    }

    function hideIosModal() {
        document.getElementById('iosModal').classList.add('hidden');
    }

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-4 py-2 rounded-full text-sm z-50';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    // Hide quick install if already installed
    if (window.matchMedia('(display-mode: standalone)').matches) {
        document.getElementById('quickInstallBtn').style.display = 'none';
    }
</script>
@endsection
