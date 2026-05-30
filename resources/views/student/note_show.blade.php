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
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
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
    <!-- Browser iframe -->
    <iframe
        src="{{ $note->file_url }}#toolbar={{ $note->is_downloadable ? '1' : '0' }}&navpanes=0&zoom=page-fit"
        class="pdf-viewer"
        id="pdfViewer"
        type="application/pdf">
    </iframe>

    <!-- PWA PDF.js Viewer -->
    <div id="pwaPdfViewer" style="display:none;width:100%;height:100%;background:#1a1a1a;overflow-y:auto;overflow-x:hidden;">
        <div id="pdfLoading" style="display:flex;align-items:center;justify-content:center;height:100%;color:#9ca3af;flex-direction:column;gap:12px;">
            <svg width="48" height="48" fill="none" stroke="#10b981" viewBox="0 0 24 24" style="animation:spin 1s linear infinite;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span style="font-size:14px;">Loading PDF...</span>
        </div>
        <div id="pdfPages" style="display:none;padding:16px;display:flex;flex-direction:column;align-items:center;gap:16px;padding-bottom:80px;"></div>
        <div id="pdfError" style="display:none;align-items:center;justify-content:center;height:100%;color:#9ca3af;flex-direction:column;gap:16px;text-align:center;padding:24px;">
            <svg width="48" height="48" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p style="font-size:14px;">Could not load PDF.</p>
            <a href="{{ $note->file_url }}" target="_blank" style="background:linear-gradient(135deg,#3b82f6,#2563eb);color:white;padding:10px 20px;border-radius:8px;font-weight:600;font-size:13px;text-decoration:none;">Open in Browser</a>
        </div>
    </div>

    <!-- PWA Bottom Controls -->
    <div id="pwaControls" style="display:none;position:fixed;bottom:0;left:0;right:0;background:rgba(0,0,0,0.85);backdrop-filter:blur(10px);padding:12px 16px;z-index:200;display:flex;align-items:center;justify-content:center;gap:20px;border-top:1px solid rgba(255,255,255,0.1);">
        <button onclick="pdfPrevPage()" style="background:none;border:none;color:white;padding:8px;"><svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
        <span id="pdfPageNum" style="color:white;font-size:13px;font-weight:600;min-width:60px;text-align:center;">1 / 1</span>
        <button onclick="pdfNextPage()" style="background:none;border:none;color:white;padding:8px;"><svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
        <button onclick="pdfZoomOut()" style="background:none;border:none;color:white;padding:8px;font-size:18px;font-weight:bold;">-</button>
        <span id="pdfZoomLevel" style="color:white;font-size:12px;min-width:40px;text-align:center;">100%</span>
        <button onclick="pdfZoomIn()" style="background:none;border:none;color:white;padding:8px;font-size:18px;font-weight:bold;">+</button>
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

    let headerVisible = true;
    let lastTap = 0;
    let pdfDoc = null;
    let currentPage = 1;
    let totalPages = 0;
    let currentZoom = 1.0;
    const pdfUrl = "{{ $note->file_url }}";

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
        if (e.target.closest('.note-header') || e.target.closest('.header-toggle') || e.target.closest('#pwaControls')) return;
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

    // ===== PDF.js Viewer for PWA =====
    function initPdfViewer() {
        if (!isPWA()) return; // Use iframe for regular browsers

        document.getElementById('pdfViewer').style.display = 'none';
        document.getElementById('touchHint').style.display = 'none';
        document.getElementById('pwaPdfViewer').style.display = 'block';
        document.getElementById('pwaControls').style.display = 'flex';

        // Load PDF.js from CDN
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
        script.onload = function() {
            pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
            loadPdf(pdfUrl);
        };
        script.onerror = function() {
            showPdfError();
        };
        document.head.appendChild(script);
    }

    function loadPdf(url) {
        pdfjsLib.getDocument({url: url, withCredentials: true}).promise.then(function(doc) {
            pdfDoc = doc;
            totalPages = doc.numPages;
            document.getElementById('pdfLoading').style.display = 'none';
            document.getElementById('pdfPages').style.display = 'flex';
            updatePageNum();
            renderAllPages();
        }).catch(function(err) {
            console.error('PDF load error:', err);
            showPdfError();
        });
    }

    function showPdfError() {
        document.getElementById('pdfLoading').style.display = 'none';
        document.getElementById('pdfError').style.display = 'flex';
    }

    function renderAllPages() {
        const container = document.getElementById('pdfPages');
        container.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const canvas = document.createElement('canvas');
            canvas.id = 'page-' + i;
            canvas.style.maxWidth = '100%';
            canvas.style.boxShadow = '0 2px 8px rgba(0,0,0,0.5)';
            container.appendChild(canvas);
            renderPage(i, canvas);
        }
        setTimeout(setupPageTracking, 500);
    }

    function renderPage(num, canvas) {
        pdfDoc.getPage(num).then(function(page) {
            const viewport = page.getViewport({scale: currentZoom});
            const ctx = canvas.getContext('2d');
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            page.render({canvasContext: ctx, viewport: viewport});
        });
    }

    function updatePageNum() {
        document.getElementById('pdfPageNum').textContent = currentPage + ' / ' + totalPages;
    }

    function pdfPrevPage() {
        if (currentPage <= 1) return;
        currentPage--;
        updatePageNum();
        scrollToPage(currentPage);
    }

    function pdfNextPage() {
        if (currentPage >= totalPages) return;
        currentPage++;
        updatePageNum();
        scrollToPage(currentPage);
    }

    function scrollToPage(num) {
        const canvas = document.getElementById('page-' + num);
        if (canvas) {
            canvas.scrollIntoView({behavior: 'smooth', block: 'start'});
        }
    }

    function pdfZoomIn() {
        if (currentZoom >= 3.0) return;
        currentZoom += 0.2;
        document.getElementById('pdfZoomLevel').textContent = Math.round(currentZoom * 100) + '%';
        renderAllPages();
    }

    function pdfZoomOut() {
        if (currentZoom <= 0.4) return;
        currentZoom -= 0.2;
        document.getElementById('pdfZoomLevel').textContent = Math.round(currentZoom * 100) + '%';
        renderAllPages();
    }

    // IntersectionObserver to track current page while scrolling
    function setupPageTracking() {
        const container = document.getElementById('pwaPdfViewer');
        if (!container) return;
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const pageNum = parseInt(entry.target.id.replace('page-', ''));
                    if (!isNaN(pageNum)) {
                        currentPage = pageNum;
                        updatePageNum();
                    }
                }
            });
        }, {root: container, threshold: 0.5});

        document.querySelectorAll('#pdfPages canvas').forEach(function(canvas) {
            observer.observe(canvas);
        });
    }

    // Initialize
    initPdfViewer();
</script>
@endsection
