@extends('layouts.student_mobile')

@section('title', $note->title)

@section('mobile-content')
<style>
    body {
        overflow: hidden;
        touch-action: none;
    }
    .pdf-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        border: none;
        background: #1a1a1a;
        z-index: 1;
    }
    .pdf-viewer {
        width: 100%;
        height: 100%;
        border: none;
    }
    .note-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #10b981, #059669);
        padding: 12px 16px;
        color: white;
        z-index: 100;
        transition: transform 0.3s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    .note-header.hidden {
        transform: translateY(-100%);
    }
    .download-btn {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        font-size: 12px;
    }
    .view-only-badge {
        background: rgba(255,255,255,0.2);
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .header-toggle {
        position: fixed;
        top: 8px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 4px;
        background: rgba(255,255,255,0.5);
        border-radius: 2px;
        z-index: 101;
        cursor: pointer;
    }
    .header-toggle:hover {
        background: rgba(255,255,255,0.8);
    }
    .touch-hint {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 12px;
        z-index: 99;
        opacity: 0;
        transition: opacity 0.3s;
        pointer-events: none;
    }
    .touch-hint.show {
        opacity: 1;
    }
    .back-btn {
        position: fixed;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        background: rgba(0,0,0,0.5);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 98;
        cursor: pointer;
        border: none;
    }
</style>

<div class="header-toggle" onclick="toggleHeader()"></div>

<div class="note-header" id="header">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-lg font-bold truncate">{{ $note->title }}</h1>
            <p class="text-xs text-emerald-100">
                📄 {{ $note->file_size_formatted }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($note->is_downloadable)
                <a href="{{ $note->file_url }}" download class="download-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download
                </a>
            @else
                <span class="view-only-badge">👁 View Only</span>
            @endif
            <button onclick="toggleHeader()" class="p-2 text-white/80 hover:text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<a href="{{ url()->previous() }}" class="back-btn">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
    </svg>
</a>

<div class="pdf-container">
    <iframe 
        src="{{ $note->file_url }}#toolbar={{ $note->is_downloadable ? '1' : '0' }}&navpanes=0&zoom=page-fit" 
        class="pdf-viewer"
        id="pdfViewer"
        type="application/pdf">
    </iframe>
</div>

<div class="touch-hint" id="touchHint">
    👆 Tap to show/hide • 🤏 Pinch to zoom • 👈👉 Swipe edges to navigate
</div>

<script>
    let headerVisible = true;
    let lastTap = 0;
    
    function toggleHeader() {
        const header = document.getElementById('header');
        headerVisible = !headerVisible;
        if (headerVisible) {
            header.classList.remove('hidden');
        } else {
            header.classList.add('hidden');
        }
    }
    
    // Auto-hide header after 3 seconds
    setTimeout(() => {
        if (headerVisible) toggleHeader();
    }, 3000);
    
    // Show touch hint on tap
    function showTouchHint() {
        const hint = document.getElementById('touchHint');
        hint.classList.add('show');
        setTimeout(() => {
            hint.classList.remove('show');
        }, 3000);
    }
    
    // Show header and hint on tap
    document.addEventListener('click', (e) => {
        if (e.target.closest('.note-header') || e.target.closest('.header-toggle')) return;
        const currentTime = new Date().getTime();
        if (currentTime - lastTap < 300) {
            // Double tap - toggle zoom could be added here
        }
        lastTap = currentTime;
        if (!headerVisible) toggleHeader();
        showTouchHint();
    });
    
    // Show hint on page load
    setTimeout(() => {
        showTouchHint();
    }, 1000);
    
    // Handle fullscreen on mobile
    if (document.documentElement.requestFullscreen) {
        document.addEventListener('dblclick', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        });
    }
</script>
@endsection
