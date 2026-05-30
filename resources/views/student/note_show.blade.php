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

<div class="pdf-container" id="pdfContainer">
    <iframe
        src="{{ $note->file_url }}#toolbar={{ $note->is_downloadable ? '1' : '0' }}&navpanes=0&zoom=page-fit"
        class="pdf-viewer"
        id="pdfViewer"
        type="application/pdf">
    </iframe>

    <!-- PWA Fallback -->
    <div id="pwaFallback" style="display:none;width:100%;height:100%;background:#1a1a1a;color:white;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:24px;">
        <svg width="64" height="64" fill="none" stroke="#10b981" viewBox="0 0 24 24" style="margin-bottom:16px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <h2 style="font-size:18px;font-weight:700;margin-bottom:8px;">PDF Viewer</h2>
        <p style="font-size:14px;color:#9ca3af;margin-bottom:24px;max-width:280px;">
            The built-in PDF viewer is not available inside the installed app.
        </p>
        <a href="{{ $note->file_url }}" target="_blank" id="openInBrowserBtn" style="background:linear-gradient(135deg,#3b82f6,#2563eb);color:white;padding:14px 28px;border-radius:12px;font-weight:600;font-size:15px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m-6-6L10 14"/></svg>
            Open in Browser
        </a>
        @if($note->is_downloadable)
        <a href="{{ $note->file_url }}" download style="margin-top:12px;color:#9ca3af;font-size:13px;text-decoration:underline;">
            Or download file
        </a>
        @endif
    </div>
</div>

<div class="touch-hint" id="touchHint">
    👆 Tap to show/hide • 🤏 Pinch to zoom • 👈👉 Swipe edges to navigate
</div>

<script>
    // Detect if running inside installed PWA (standalone mode)
    function isPWA() {
        return window.matchMedia('(display-mode: standalone)').matches ||
               window.navigator.standalone === true;
    }

    // If in PWA mode, hide iframe and show fallback
    if (isPWA()) {
        document.getElementById('pdfViewer').style.display = 'none';
        document.getElementById('touchHint').style.display = 'none';
        const fallback = document.getElementById('pwaFallback');
        fallback.style.display = 'flex';
    }

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
