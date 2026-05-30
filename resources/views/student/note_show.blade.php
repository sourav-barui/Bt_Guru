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
    @keyframes fadeOut {
        0% { opacity: 1; visibility: visible; }
        80% { opacity: 1; visibility: visible; }
        100% { opacity: 0; visibility: hidden; }
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
    <div id="pwaControls" style="position:fixed;bottom:0;left:0;right:0;background:rgba(0,0,0,0.9);backdrop-filter:blur(10px);padding:8px 12px;z-index:200;display:none;align-items:center;justify-content:space-between;border-top:1px solid rgba(255,255,255,0.1);gap:8px;">
        <div style="display:flex;align-items:center;gap:4px;">
            <button onclick="pdfZoomOut()" style="background:rgba(255,255,255,0.1);border:none;color:white;padding:8px;border-radius:8px;display:flex;align-items:center;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/></svg>
            </button>
            <span id="pdfZoomLevel" style="color:white;font-size:12px;min-width:36px;text-align:center;">100%</span>
            <button onclick="pdfZoomIn()" style="background:rgba(255,255,255,0.1);border:none;color:white;padding:8px;border-radius:8px;display:flex;align-items:center;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m0 0v6m0-6h6m-6 0H4"/></svg>
            </button>
        </div>
        <div style="display:flex;align-items:center;gap:12px;">
            <button onclick="pdfPrevPage()" id="btnPrev" style="background:none;border:none;color:white;padding:8px;opacity:0.4;transition:opacity 0.2s;"><svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
            <span id="pdfPageNum" style="color:white;font-size:13px;font-weight:600;letter-spacing:1px;min-width:50px;text-align:center;">1 / 1</span>
            <button onclick="pdfNextPage()" id="btnNext" style="background:none;border:none;color:white;padding:8px;opacity:1;transition:opacity 0.2s;"><svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
        </div>
        <div style="display:flex;align-items:center;gap:4px;">
            <button onclick="togglePageList()" style="background:rgba(255,255,255,0.1);border:none;color:white;padding:8px;border-radius:8px;display:flex;align-items:center;" title="Page list">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <button onclick="resetZoom()" style="background:rgba(255,255,255,0.1);border:none;color:white;padding:8px;border-radius:8px;display:flex;align-items:center;" title="Fit to screen">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
            </button>
        </div>
    </div>

    <!-- First-use swipe hint overlay -->
    <div id="swipeHint" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.7);z-index:250;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:16px;text-align:center;padding:24px;animation:fadeOut 3s forwards;animation-delay:2s;">
        <div style="display:flex;align-items:center;gap:40px;color:white;">
            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.6;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
                <svg width="64" height="64" fill="none" stroke="white" viewBox="0 0 24 24" style="opacity:0.9;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"/></svg>
                <span style="font-size:16px;font-weight:600;">Swipe or use arrows to change pages</span>
                <span style="font-size:13px;opacity:0.7;">Scroll to zoom • Drag thumbnail to pan</span>
            </div>
            <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="opacity:0.6;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </div>

    <!-- Page list overlay -->
    <div id="pageListOverlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.92);z-index:220;flex-direction:column;overflow-y:auto;padding:16px;padding-bottom:80px;">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0 16px;position:sticky;top:0;background:rgba(0,0,0,0.92);z-index:5;">
            <span style="color:white;font-size:16px;font-weight:600;">Select Page</span>
            <button onclick="togglePageList()" style="background:none;border:none;color:white;padding:8px;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div id="pageListGrid" style="display:grid;grid-template-columns:repeat(auto-fill, minmax(100px, 1fr));gap:12px;">
            <!-- Page thumbnails injected here -->
        </div>
    </div>

    <!-- Thumbnail navigator (shows when zoomed in) -->
    <div id="thumbNav" style="display:none;position:fixed;bottom:80px;right:12px;width:100px;height:140px;background:rgba(0,0,0,0.85);border:1px solid rgba(255,255,255,0.2);border-radius:8px;overflow:hidden;z-index:210;">
        <canvas id="thumbCanvas" style="width:100%;height:100%;"></canvas>
        <div id="thumbViewport" style="position:absolute;border:2px solid #3b82f6;background:rgba(59,130,246,0.15);border-radius:2px;cursor:grab;"></div>
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
        // Try direct load first
        pdfjsLib.getDocument({url: url, withCredentials: true}).promise.then(function(doc) {
            onPdfLoaded(doc);
        }).catch(function(err) {
            console.warn('Direct PDF load failed, trying fetch fallback:', err);
            // Fallback: fetch via XHR then load from blob
            fetch(url, {credentials: 'include'}).then(function(res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.arrayBuffer();
            }).then(function(buffer) {
                const typedArray = new Uint8Array(buffer);
                pdfjsLib.getDocument({data: typedArray}).promise.then(function(doc) {
                    onPdfLoaded(doc);
                });
            }).catch(function(err2) {
                console.error('Fetch fallback also failed:', err2);
                showPdfError();
            });
        });
    }

    function onPdfLoaded(doc) {
        pdfDoc = doc;
        totalPages = doc.numPages;
        document.getElementById('pdfLoading').style.display = 'none';
        document.getElementById('singlePageContainer').style.display = 'flex';
        document.getElementById('tapLeft').style.display = 'block';
        document.getElementById('tapRight').style.display = 'block';
        updatePageNum();
        renderCurrentPage();
        showSwipeHint();
    }

    function showPdfError() {
        document.getElementById('pdfLoading').style.display = 'none';
        document.getElementById('pdfError').style.display = 'flex';
    }

    const studentName = "{{ Auth::user()->name }}";
    const studentPhone = "{{ Auth::user()->phone ?? 'N/A' }}";
    const tenantName = "{{ $currentTenant->coaching_name ?? 'BT Guru' }}";
    const tenantUrl = "{{ $currentTenant->custom_domain ?? ($currentTenant->subdomain . '.' . config('app.central_domain', 'btguru.tech')) }}";

    function drawWatermark(ctx, width, height) {
        ctx.save();
        ctx.translate(width / 2, height / 2);
        ctx.rotate(-Math.PI / 5);

        const fontSize = Math.max(12, Math.floor(width / 18));

        // Shadow for better contrast on any background
        ctx.shadowColor = 'rgba(0, 0, 0, 0.3)';
        ctx.shadowBlur = 4;
        ctx.shadowOffsetX = 1;
        ctx.shadowOffsetY = 1;

        ctx.fillStyle = 'rgba(100, 100, 100, 0.22)';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';

        const lineGap = fontSize * 1.15;

        // Line 1: Tenant name (bold, largest)
        ctx.font = 'bold ' + fontSize + 'px sans-serif';
        ctx.fillText(tenantName, 0, -lineGap * 1.5);

        // Line 2: Tenant URL
        ctx.font = 'normal ' + Math.floor(fontSize * 0.72) + 'px sans-serif';
        ctx.fillText(tenantUrl, 0, -lineGap * 0.25);

        // Line 3: Student name
        ctx.font = 'bold ' + Math.floor(fontSize * 0.88) + 'px sans-serif';
        ctx.fillText(studentName, 0, lineGap * 1.1);

        // Line 4: Student phone
        ctx.font = 'normal ' + Math.floor(fontSize * 0.72) + 'px sans-serif';
        ctx.fillText(studentPhone, 0, lineGap * 2.2);

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
            const fitScale = Math.min(maxWidth / viewport.width, maxHeight / viewport.height, 3);

            // Use device pixel ratio for crisp rendering (2x or 3x on Retina/mobile)
            const dpr = Math.min(window.devicePixelRatio || 1, 3);
            const renderScale = fitScale * dpr;

            const renderViewport = page.getViewport({scale: renderScale});
            const ctx = canvas.getContext('2d');

            // High-res canvas backing store
            canvas.width = renderViewport.width;
            canvas.height = renderViewport.height;
            canvasW = renderViewport.width;
            canvasH = renderViewport.height;

            // Display size fits container (CSS pixels)
            canvas.style.width = (renderViewport.width / dpr) + 'px';
            canvas.style.height = (renderViewport.height / dpr) + 'px';

            const renderTask = page.render({canvasContext: ctx, viewport: renderViewport});
            renderTask.promise.then(function() {
                drawWatermark(ctx, canvas.width, canvas.height);
                renderThumbnail();
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
        resetZoom();
        updatePageNum();
        renderCurrentPage();
    }

    function pdfNextPage() {
        if (currentPage >= totalPages) return;
        currentPage++;
        resetZoom();
        updatePageNum();
        renderCurrentPage();
    }

    // ===== Zoom via CSS transform (smooth on mobile) =====
    let cssZoom = 1.0;
    let panX = 0;
    let panY = 0;
    let canvasW = 0;
    let canvasH = 0;

    function applyTransform() {
        const canvas = document.getElementById('currentPageCanvas');
        canvas.style.transform = 'translate(' + panX + 'px,' + panY + 'px) scale(' + cssZoom + ')';
        canvas.style.transformOrigin = 'center center';
        document.getElementById('pdfZoomLevel').textContent = Math.round(cssZoom * 100) + '%';
        updateThumbViewport();
        toggleThumbNav();
    }

    function pdfZoomIn() {
        if (cssZoom >= 3.0) return;
        cssZoom = Math.min(3.0, cssZoom + 0.3);
        applyTransform();
    }

    function pdfZoomOut() {
        if (cssZoom <= 0.5) return;
        cssZoom = Math.max(0.5, cssZoom - 0.3);
        applyTransform();
    }

    function resetZoom() {
        cssZoom = 1.0;
        panX = 0;
        panY = 0;
        applyTransform();
    }

    // ===== Mouse wheel zoom (desktop, only over PDF) =====
    document.getElementById('pdfContainer').addEventListener('wheel', function(e) {
        if (e.ctrlKey || e.metaKey) return;
        e.preventDefault();
        const delta = e.deltaY > 0 ? -0.2 : 0.2;
        const newZoom = Math.min(3.0, Math.max(0.5, cssZoom + delta));
        if (newZoom !== cssZoom) {
            cssZoom = newZoom;
            applyTransform();
        }
    }, {passive: false});

    // ===== Mouse drag to pan (desktop) =====
    let isMouseDragging = false;
    let mouseStart = { x: 0, y: 0, panX: 0, panY: 0 };
    const pdfContainer = document.getElementById('pdfContainer');

    pdfContainer.addEventListener('mousedown', function(e) {
        if (e.button !== 0) return; // Only left click
        isMouseDragging = true;
        mouseStart.x = e.clientX;
        mouseStart.y = e.clientY;
        mouseStart.panX = panX;
        mouseStart.panY = panY;
        pdfContainer.style.cursor = 'grabbing';
        e.preventDefault();
    });

    document.addEventListener('mousemove', function(e) {
        if (!isMouseDragging) return;
        const dx = e.clientX - mouseStart.x;
        const dy = e.clientY - mouseStart.y;
        panX = mouseStart.panX + dx;
        panY = mouseStart.panY + dy;
        applyTransform();
    });

    document.addEventListener('mouseup', function() {
        isMouseDragging = false;
        pdfContainer.style.cursor = 'default';
    });

    // ===== Thumbnail Navigator =====
    function toggleThumbNav() {
        const nav = document.getElementById('thumbNav');
        if (cssZoom > 1.05) {
            nav.style.display = 'block';
            renderThumbnail();
        } else {
            nav.style.display = 'none';
        }
    }

    function renderThumbnail() {
        const canvas = document.getElementById('currentPageCanvas');
        const thumbCanvas = document.getElementById('thumbCanvas');
        const thumbCtx = thumbCanvas.getContext('2d');
        thumbCanvas.width = 100;
        thumbCanvas.height = 140;
        thumbCtx.clearRect(0, 0, 100, 140);
        if (canvas.width && canvas.height) {
            const scale = Math.min(100 / canvas.width, 140 / canvas.height);
            const dw = canvas.width * scale;
            const dh = canvas.height * scale;
            thumbCtx.drawImage(canvas, (100 - dw) / 2, (140 - dh) / 2, dw, dh);
        }
        updateThumbViewport();
    }

    function updateThumbViewport() {
        const vp = document.getElementById('thumbViewport');
        if (cssZoom <= 1.05) { vp.style.display = 'none'; return; }
        vp.style.display = 'block';
        const container = document.getElementById('singlePageContainer');
        const thumbW = 100;
        const thumbH = 140;
        const ratioX = thumbW / (canvasW * cssZoom);
        const ratioY = thumbH / (canvasH * cssZoom);
        const vw = thumbW * ratioX * (container.clientWidth / canvasW);
        const vh = thumbH * ratioY * (container.clientHeight / canvasH);
        const vx = (thumbW - vw) / 2 - panX * ratioX;
        const vy = (thumbH - vh) / 2 - panY * ratioY;
        vp.style.width = Math.max(8, vw) + 'px';
        vp.style.height = Math.max(8, vh) + 'px';
        vp.style.left = Math.max(0, Math.min(thumbW - vw, vx)) + 'px';
        vp.style.top = Math.max(0, Math.min(thumbH - vh, vy)) + 'px';
    }

    // Thumbnail drag to pan
    let thumbDragging = false;
    let thumbStart = { x: 0, y: 0, panX: 0, panY: 0 };
    const thumbVp = document.getElementById('thumbViewport');
    thumbVp.addEventListener('mousedown', function(e) {
        thumbDragging = true;
        thumbStart.x = e.clientX;
        thumbStart.y = e.clientY;
        thumbStart.panX = panX;
        thumbStart.panY = panY;
        thumbVp.style.cursor = 'grabbing';
        e.preventDefault();
    });
    document.addEventListener('mousemove', function(e) {
        if (!thumbDragging) return;
        const dx = (e.clientX - thumbStart.x) * (canvasW * cssZoom / 100);
        const dy = (e.clientY - thumbStart.y) * (canvasH * cssZoom / 140);
        panX = thumbStart.panX - dx;
        panY = thumbStart.panY - dy;
        applyTransform();
    });
    document.addEventListener('mouseup', function() {
        thumbDragging = false;
        thumbVp.style.cursor = 'grab';
    });

    // ===== Touch handling: pinch-zoom + swipe + pan =====
    let touchState = { x: 0, y: 0, startX: 0, startY: 0, startDist: 0, isPinching: false, isPanning: false };

    document.addEventListener('touchstart', function(e) {
        if (e.touches.length === 2) {
            touchState.isPinching = true;
            touchState.startDist = Math.hypot(
                e.touches[0].clientX - e.touches[1].clientX,
                e.touches[0].clientY - e.touches[1].clientY
            );
            touchState.startZoom = cssZoom;
        } else if (e.touches.length === 1) {
            touchState.startX = e.touches[0].clientX;
            touchState.startY = e.touches[0].clientY;
            touchState.x = e.touches[0].clientX;
            touchState.y = e.touches[0].clientY;
            touchState.isPanning = cssZoom > 1.0;
        }
    }, {passive: false});

    document.addEventListener('touchmove', function(e) {
        if (e.touches.length === 2 && touchState.isPinching) {
            e.preventDefault();
            const dist = Math.hypot(
                e.touches[0].clientX - e.touches[1].clientX,
                e.touches[0].clientY - e.touches[1].clientY
            );
            const scale = dist / touchState.startDist;
            cssZoom = Math.min(3.0, Math.max(0.5, touchState.startZoom * scale));
            applyTransform();
        } else if (e.touches.length === 1 && touchState.isPanning) {
            e.preventDefault();
            const dx = e.touches[0].clientX - touchState.x;
            const dy = e.touches[0].clientY - touchState.y;
            panX += dx;
            panY += dy;
            touchState.x = e.touches[0].clientX;
            touchState.y = e.touches[0].clientY;
            applyTransform();
        }
    }, {passive: false});

    document.addEventListener('touchend', function(e) {
        if (touchState.isPinching) {
            touchState.isPinching = false;
            return;
        }
        if (touchState.isPanning) {
            touchState.isPanning = false;
            return;
        }
        if (e.changedTouches.length === 1) {
            const dx = e.changedTouches[0].clientX - touchState.startX;
            const dy = e.changedTouches[0].clientY - touchState.startY;
            if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 60) {
                if (dx < 0) {
                    pdfNextPage();
                } else {
                    pdfPrevPage();
                }
            }
        }
    }, {passive: false});

    // Double-tap to reset zoom
    let lastTapTime = 0;
    document.addEventListener('touchend', function(e) {
        const now = new Date().getTime();
        if (now - lastTapTime < 300) {
            resetZoom();
        }
        lastTapTime = now;
    });

    // ===== Page List Overlay =====
    function togglePageList() {
        const overlay = document.getElementById('pageListOverlay');
        if (overlay.style.display === 'flex') {
            overlay.style.display = 'none';
        } else {
            overlay.style.display = 'flex';
            renderPageList();
        }
    }

    function renderPageList() {
        const grid = document.getElementById('pageListGrid');
        grid.innerHTML = '';
        for (let i = 1; i <= totalPages; i++) {
            const item = document.createElement('div');
            item.style.cssText = 'display:flex;flex-direction:column;align-items:center;gap:6px;cursor:pointer;padding:8px;border-radius:8px;background:rgba(255,255,255,0.05);border:2px solid ' + (i === currentPage ? '#3b82f6' : 'transparent') + ';';
            item.onclick = function() { goToPage(i); };

            const thumb = document.createElement('canvas');
            thumb.width = 100;
            thumb.height = 140;
            thumb.style.cssText = 'width:100%;height:auto;border-radius:4px;background:#1a1a2e;';
            item.appendChild(thumb);

            const label = document.createElement('span');
            label.textContent = 'Page ' + i;
            label.style.cssText = 'color:white;font-size:11px;';
            item.appendChild(label);

            grid.appendChild(item);

            // Render thumbnail for this page
            (function(pageNum, cvs) {
                if (!pdfDoc) return;
                pdfDoc.getPage(pageNum).then(function(page) {
                    const vp = page.getViewport({scale: Math.min(100 / page.getViewport({scale:1}).width, 140 / page.getViewport({scale:1}).height)});
                    cvs.width = vp.width;
                    cvs.height = vp.height;
                    page.render({canvasContext: cvs.getContext('2d'), viewport: vp});
                });
            })(i, thumb);
        }
    }

    function goToPage(num) {
        if (num < 1 || num > totalPages) return;
        currentPage = num;
        resetZoom();
        updatePageNum();
        renderCurrentPage();
        togglePageList();
    }

    // ===== Show hint on first view (once per session) =====
    function showSwipeHint() {
        if (sessionStorage.getItem('pdfHintShown')) return;
        const hint = document.getElementById('swipeHint');
        hint.style.display = 'flex';
        sessionStorage.setItem('pdfHintShown', 'true');
        setTimeout(function() {
            hint.style.display = 'none';
        }, 5000);
    }

    // Initialize
    initPdfViewer();
</script>
@endsection
