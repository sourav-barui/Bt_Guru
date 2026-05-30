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

    <!-- PWA Single-Page Image Viewer -->
    <div id="pwaPdfViewer" style="display:none;width:100%;height:100%;background:#1a1a1a;position:relative;overflow:hidden;">
        <!-- Loading -->
        <div id="pdfLoading" style="display:flex;align-items:center;justify-content:center;height:100%;color:#9ca3af;flex-direction:column;gap:12px;position:absolute;inset:0;z-index:10;">
            <svg width="48" height="48" fill="none" stroke="#10b981" viewBox="0 0 24 24" style="animation:spin 1s linear infinite;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            <span style="font-size:14px;">Loading PDF...</span>
        </div>

        <!-- Single Page Canvas Container -->
        <div id="singlePageContainer" style="display:none;width:100%;height:100%;display:flex;align-items:center;justify-content:center;padding:16px;padding-bottom:70px;">
            <canvas id="currentPageCanvas" style="max-width:100%;max-height:100%;box-shadow:0 4px 20px rgba(0,0,0,0.6);border-radius:4px;"></canvas>
        </div>

        <!-- Error -->
        <div id="pdfError" style="display:none;align-items:center;justify-content:center;height:100%;color:#9ca3af;flex-direction:column;gap:16px;text-align:center;padding:24px;position:absolute;inset:0;z-index:10;">
            <svg width="48" height="48" fill="none" stroke="#ef4444" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <p style="font-size:14px;">Could not load PDF.</p>
            <a href="{{ $note->file_url }}" target="_blank" style="background:linear-gradient(135deg,#3b82f6,#2563eb);color:white;padding:10px 20px;border-radius:8px;font-weight:600;font-size:13px;text-decoration:none;">Open in Browser</a>
        </div>
    </div>

    <!-- PWA Bottom Controls -->
    <div id="pwaControls" style="display:none;position:fixed;bottom:0;left:0;right:0;background:rgba(0,0,0,0.9);backdrop-filter:blur(10px);padding:10px 16px;z-index:200;display:flex;align-items:center;justify-content:space-between;border-top:1px solid rgba(255,255,255,0.1);">
        <button onclick="pdfPrevPage()" id="btnPrev" style="background:none;border:none;color:white;padding:10px;opacity:0.4;transition:opacity 0.2s;"><svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
        <span id="pdfPageNum" style="color:white;font-size:14px;font-weight:600;letter-spacing:1px;">1 / 1</span>
        <button onclick="pdfNextPage()" id="btnNext" style="background:none;border:none;color:white;padding:10px;opacity:1;transition:opacity 0.2s;"><svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
    </div>

    <!-- Tap zones for page navigation -->
    <div id="tapLeft" onclick="pdfPrevPage()" style="display:none;position:fixed;left:0;top:100px;bottom:70px;width:25%;z-index:90;cursor:pointer;"></div>
    <div id="tapRight" onclick="pdfNextPage()" style="display:none;position:fixed;right:0;top:100px;bottom:70px;width:25%;z-index:90;cursor:pointer;"></div>
</div>

<div class="touch-hint" id="touchHint">
    👆 Tap to show/hide • 🤏 Pinch to zoom • 👈👉 Swipe edges to navigate
</div>

<script>
    // Detect if running inside installed PWA (standalone mode)
    // Add ?pwa=1 to URL to force canvas viewer (for testing)
    function isPWA() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('pwa') === '1') return true;
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

    // ===== PDF.js Canvas Viewer (All Modes: Web + PWA) =====
    function initPdfViewer() {
        // Always use canvas viewer with watermark
        document.getElementById('pdfViewer').style.display = 'none';
        document.getElementById('touchHint').style.display = 'none';
        document.getElementById('pwaPdfViewer').style.display = 'block';
        document.getElementById('pwaControls').style.display = 'flex';
        document.getElementById('tapLeft').style.display = 'block';
        document.getElementById('tapRight').style.display = 'block';

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
            document.getElementById('singlePageContainer').style.display = 'flex';
            document.getElementById('tapLeft').style.display = 'block';
            document.getElementById('tapRight').style.display = 'block';
            updatePageNum();
            renderCurrentPage();
        }).catch(function(err) {
            console.error('PDF load error:', err);
            showPdfError();
        });
    }

    function showPdfError() {
        document.getElementById('pdfLoading').style.display = 'none';
        document.getElementById('pdfError').style.display = 'flex';
    }

    const tenantName = "{{ $currentTenant->coaching_name ?? 'BT Guru' }}";

    function drawWatermark(ctx, width, height) {
        ctx.save();
        ctx.font = 'bold ' + Math.max(14, Math.floor(width / 15)) + 'px sans-serif';
        ctx.fillStyle = 'rgba(128, 128, 128, 0.15)';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.translate(width / 2, height / 2);
        ctx.rotate(-Math.PI / 6);
        ctx.fillText(tenantName, 0, 0);
        ctx.restore();
    }

    function renderCurrentPage() {
        const canvas = document.getElementById('currentPageCanvas');
        pdfDoc.getPage(currentPage).then(function(page) {
            // Fit page to viewport while maintaining aspect ratio
            const container = document.getElementById('singlePageContainer');
            const maxWidth = container.clientWidth - 32;
            const maxHeight = container.clientHeight - 32;
            const viewport = page.getViewport({scale: 1});
            const scale = Math.min(maxWidth / viewport.width, maxHeight / viewport.height, 2);

            const renderViewport = page.getViewport({scale: scale});
            const ctx = canvas.getContext('2d');
            canvas.width = renderViewport.width;
            canvas.height = renderViewport.height;

            const renderTask = page.render({canvasContext: ctx, viewport: renderViewport});
            renderTask.promise.then(function() {
                drawWatermark(ctx, canvas.width, canvas.height);
            });
        });
    }

    function updatePageNum() {
        document.getElementById('pdfPageNum').textContent = currentPage + ' / ' + totalPages;
        // Update button opacity
        document.getElementById('btnPrev').style.opacity = currentPage <= 1 ? '0.3' : '1';
        document.getElementById('btnNext').style.opacity = currentPage >= totalPages ? '0.3' : '1';
    }

    function pdfPrevPage() {
        if (currentPage <= 1) return;
        currentPage--;
        updatePageNum();
        renderCurrentPage();
    }

    function pdfNextPage() {
        if (currentPage >= totalPages) return;
        currentPage++;
        updatePageNum();
        renderCurrentPage();
    }

    // Swipe support for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', function(e) {
        touchStartX = e.changedTouches[0].screenX;
    }, false);

    document.addEventListener('touchend', function(e) {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, false);

    function handleSwipe() {
        const swipeThreshold = 50;
        if (touchEndX < touchStartX - swipeThreshold) {
            // Swipe left - next page
            pdfNextPage();
        }
        if (touchEndX > touchStartX + swipeThreshold) {
            // Swipe right - prev page
            pdfPrevPage();
        }
    }

    // Initialize
    initPdfViewer();
</script>
@endsection
