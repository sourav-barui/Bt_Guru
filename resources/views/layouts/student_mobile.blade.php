@php
    $tenant = app('current_tenant');
    $tenantSettings = $tenant?->settings ?? [];
    $coachingName = $tenant?->coaching_name ?? 'BT Guru';
    $portalTitle = $tenantSettings['portal_title'] ?? ($coachingName . ' - Student Portal');
    $shortName = $coachingName;
    $portalIcon = $tenant?->portal_icon ?? $tenant?->pwa_icon ?? null;
    $pwaIcon = $tenant?->pwa_icon ?? $tenant?->logo ?? null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#7C3AED">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ route('tenant.manifest') }}">
    
    <!-- Icons -->
    @if($portalIcon)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($portalIcon) }}">
        <link rel="apple-touch-icon" href="{{ Storage::url($portalIcon) }}">
    @else
        <link rel="apple-touch-icon" href="/build/icon-placeholder.svg">
    @endif
    
    <title>@yield('title', 'Student Portal') - {{ $shortName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { -webkit-tap-highlight-color: transparent; box-sizing: border-box; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f8f7fa; }
        .mobile-container { max-width: 100%; overflow-x: hidden; }
        
        /* Testbook Style Bottom Navigation */
        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: white; border-top: 1px solid #e5e7eb; z-index: 50; padding-bottom: env(safe-area-inset-bottom); }
        .nav-item { flex: 1; display: flex; flex-direction: column; align-items: center; padding: 8px 4px; color: #9ca3af; transition: all 0.2s; }
        .nav-item.active { color: #7c3aed; }
        .nav-item svg { width: 24px; height: 24px; margin-bottom: 2px; }
        .nav-item span { font-size: 11px; font-weight: 500; }
        
        /* Content Area */
        .content-area { padding-bottom: 80px; min-height: 100vh; background: #f8f7fa; }
        
        /* Testbook Style Header */
        .tb-header { background: white; padding: 16px; border-bottom: 1px solid #e5e7eb; position: sticky; top: 0; z-index: 40; }
        .tb-header-gradient { background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%); color: white; padding: 20px 16px; }
        
        /* Testbook Cards */
        .tb-card { background: white; border-radius: 16px; padding: 16px; margin: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #f3f4f6; }
        .tb-card-flat { background: white; border-radius: 12px; padding: 14px; margin: 8px 12px; border: 1px solid #e5e7eb; }
        
        /* Testbook Course Cards */
        .tb-course-card { background: white; border-radius: 16px; overflow: hidden; margin: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .tb-course-image { height: 120px; background: linear-gradient(135deg, #7c3aed, #a855f7); display: flex; align-items: center; justify-content: center; position: relative; }
        .tb-course-badge { position: absolute; top: 12px; right: 12px; background: rgba(255,255,255,0.95); color: #7c3aed; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        
        /* Testbook Grid */
        .tb-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; padding: 0 12px; }
        .tb-grid-item { background: white; border-radius: 12px; padding: 16px; text-align: center; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px rgba(0,0,0,0.04); }
        .tb-grid-icon { width: 48px; height: 48px; background: linear-gradient(135deg, #f3e8ff, #e9d5ff); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; color: #7c3aed; }
        
        /* Testbook Progress */
        .tb-progress-bg { height: 6px; background: #e5e7eb; border-radius: 10px; overflow: hidden; }
        .tb-progress-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg, #7c3aed, #a855f7); transition: width 0.3s; }
        .tb-progress-text { font-size: 12px; color: #7c3aed; font-weight: 600; }
        
        /* Testbook Buttons */
        .tb-btn-primary { background: linear-gradient(135deg, #7c3aed, #5b21b6); color: white; padding: 14px 24px; border-radius: 12px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: none; width: 100%; }
        .tb-btn-secondary { background: #f3e8ff; color: #7c3aed; padding: 10px 16px; border-radius: 10px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; }
        .tb-btn-outline { background: white; color: #7c3aed; border: 2px solid #7c3aed; padding: 12px 20px; border-radius: 10px; font-weight: 600; font-size: 14px; display: inline-flex; align-items: center; justify-content: center; gap: 6px; }
        
        /* Testbook Video Cards */
        .tb-video-card { background: white; border-radius: 12px; padding: 14px; margin: 10px 12px; border: 1px solid #e5e7eb; display: flex; gap: 12px; align-items: flex-start; }
        .tb-video-thumb { width: 100px; height: 70px; background: linear-gradient(135deg, #1f2937, #374151); border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; position: relative; }
        .tb-video-play { position: absolute; width: 36px; height: 36px; background: rgba(124,58,237,0.95); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; }
        .tb-video-duration { position: absolute; bottom: 4px; right: 4px; background: rgba(0,0,0,0.8); color: white; padding: 2px 6px; border-radius: 4px; font-size: 10px; }
        
        /* Testbook Section Headers */
        .tb-section-title { font-size: 18px; font-weight: 700; color: #1f2937; margin: 0; }
        .tb-section-subtitle { font-size: 14px; color: #6b7280; margin: -8px 12px 16px; }
        
        /* Testbook Stats */
        .tb-stats-row { display: flex; gap: 12px; padding: 0 12px; overflow-x: auto; }
        .tb-stat-card { background: white; border-radius: 12px; padding: 16px; min-width: 110px; text-align: center; border: 1px solid #e5e7eb; }
        .tb-stat-number { font-size: 28px; font-weight: 700; color: #7c3aed; }
        .tb-stat-label { font-size: 12px; color: #6b7280; margin-top: 4px; font-weight: 500; }
        
        /* Touch Targets */
        .touch-btn { min-height: 44px; min-width: 44px; }

        /* Notification Bell */
        .notif-bell { position: relative; display: inline-flex; align-items: center; justify-content: center; }
        .notif-badge { position: absolute; top: -4px; right: -4px; background: #ef4444; color: white; font-size: 10px; font-weight: 700; min-width: 18px; height: 18px; border-radius: 9px; display: flex; align-items: center; justify-content: center; padding: 0 4px; border: 2px solid white; }
        .notif-panel { position: fixed; top: 0; right: 0; bottom: 0; width: min(360px, 100vw); background: white; z-index: 200; transform: translateX(100%); transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); box-shadow: -8px 0 32px rgba(0,0,0,0.15); display: flex; flex-direction: column; }
        .notif-panel.open { transform: translateX(0); }
        .notif-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 199; display: none; }
        .notif-overlay.open { display: block; }
        .notif-item { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background 0.15s; }
        .notif-item:hover { background: #f9fafb; }
        .notif-item.unread { background: #faf5ff; }
        .notif-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        
        /* Horizontal Scroll Container */
        .tb-scroll-x { display: flex; gap: 12px; padding: 0 12px; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; }
        .tb-scroll-x::-webkit-scrollbar { display: none; }
        .tb-scroll-card { min-width: 260px; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06); border: 1px solid #f3f4f6; }

        /* PWA Install Banner */
        .pwa-install-banner {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
            color: white;
            padding: 12px 16px;
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            animation: slideDown 0.3s ease-out;
        }
        .pwa-install-banner.show {
            display: flex;
        }
        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
        .pwa-install-content {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }
        .pwa-install-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .pwa-install-icon img {
            width: 28px;
            height: 28px;
        }
        .pwa-install-text {
            flex: 1;
        }
        .pwa-install-title {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            line-height: 1.3;
        }
        .pwa-install-subtitle {
            font-size: 12px;
            opacity: 0.9;
            margin: 0;
            line-height: 1.3;
        }
        .pwa-install-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }
        .pwa-btn-install {
            background: white;
            color: #7c3aed;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
        }
        .pwa-btn-close {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .pwa-btn-close svg {
            width: 16px;
            height: 16px;
        }
        
        /* iOS Safari Add to Home Screen Hint */
        .pwa-ios-hint {
            position: fixed;
            bottom: 100px;
            left: 16px;
            right: 16px;
            background: #1f2937;
            color: white;
            padding: 14px 16px;
            border-radius: 12px;
            z-index: 1000;
            display: none;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        }
        .pwa-ios-hint.show {
            display: block;
            animation: fadeInUp 0.3s ease-out;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .pwa-ios-hint-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        /* Install Instructions Modal for non-Chrome browsers */
        .pwa-instructions-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.6);
            z-index: 2000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .pwa-instructions-modal.show {
            display: flex;
        }
        .pwa-instructions-content {
            background: white;
            border-radius: 20px;
            max-width: 360px;
            width: 100%;
            padding: 24px;
            text-align: center;
            animation: scaleIn 0.3s ease-out;
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .pwa-instructions-title {
            font-size: 20px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
        }
        .pwa-instructions-subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 24px;
        }
        .pwa-instructions-steps {
            text-align: left;
            margin-bottom: 24px;
        }
        .pwa-instructions-step {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .pwa-instructions-step:last-child {
            border-bottom: none;
        }
        .pwa-instructions-step-icon {
            width: 40px;
            height: 40px;
            background: #f3e8ff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7c3aed;
            flex-shrink: 0;
        }
        .pwa-instructions-step-text {
            font-size: 14px;
            color: #374151;
        }
        .pwa-instructions-close {
            background: #7c3aed;
            color: white;
            border: none;
            padding: 12px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
        }
        .pwa-ios-hint-title {
            font-size: 14px;
            font-weight: 600;
        }
        .pwa-ios-hint-close {
            background: none;
            border: none;
            color: #9ca3af;
            padding: 4px;
            cursor: pointer;
        }
        .pwa-ios-hint-body {
            font-size: 13px;
            color: #d1d5db;
            line-height: 1.5;
        }
        .pwa-ios-hint-icon {
            display: inline-flex;
            vertical-align: middle;
            margin: 0 2px;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="mobile-container">
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mx-4 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-4 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                {{ session('error') }}
            </div>
        @endif

        <!-- PWA Install Banner (Android/Chrome/Desktop) -->
        <div id="pwaInstallBanner" class="pwa-install-banner">
            <div class="pwa-install-content">
                <div class="pwa-install-icon">
                    @if($pwaIcon)
                        <img src="{{ Storage::url($pwaIcon) }}" alt="{{ $shortName }}" style="width:28px;height:28px;">
                    @else
                        <img src="/build/icon-placeholder.svg" alt="{{ $shortName }}" style="width:28px;height:28px;">
                    @endif
                </div>
                <div class="pwa-install-text">
                    <p class="pwa-install-title">Install {{ $shortName }} App</p>
                    <p class="pwa-install-subtitle">Add to home screen for quick access</p>
                </div>
            </div>
            <div class="pwa-install-actions">
                <button class="pwa-btn-install" id="pwaInstallBtn">Install</button>
                <button class="pwa-btn-close" id="pwaCloseBtn" aria-label="Close">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        <!-- iOS Add to Home Screen Hint -->
        <div id="pwaIosHint" class="pwa-ios-hint">
            <div class="pwa-ios-hint-header">
                <span class="pwa-ios-hint-title">Install {{ $shortName }}</span>
                <button class="pwa-ios-hint-close" id="pwaIosCloseBtn">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="pwa-ios-hint-body">
                Tap <svg class="pwa-ios-hint-icon" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"/></svg> then "Add to Home Screen" to install
            </div>
        </div>

        <!-- Install Instructions Modal (for non-Chrome browsers) -->
        <div id="pwaInstructionsModal" class="pwa-instructions-modal">
            <div class="pwa-instructions-content">
                <div class="pwa-instructions-title">Install {{ $shortName }} App</div>
                <div class="pwa-instructions-subtitle">Add to your home screen for quick access</div>
                <div class="pwa-instructions-steps">
                    <div class="pwa-instructions-step">
                        <div class="pwa-instructions-step-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </div>
                        <div class="pwa-instructions-step-text">
                            <strong>Step 1:</strong> Tap the menu button (⋮) in your browser
                        </div>
                    </div>
                    <div class="pwa-instructions-step">
                        <div class="pwa-instructions-step-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div class="pwa-instructions-step-text">
                            <strong>Step 2:</strong> Select "Add to Home screen" or "Install"
                        </div>
                    </div>
                    <div class="pwa-instructions-step">
                        <div class="pwa-instructions-step-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div class="pwa-instructions-step-text">
                            <strong>Step 3:</strong> Tap "Add" or "Install" to confirm
                        </div>
                    </div>
                </div>
                <button class="pwa-instructions-close" onclick="hideInstructionsModal()">Got it</button>
            </div>
        </div>

        <!-- Notification Overlay -->
        <div class="notif-overlay" id="notifOverlay" onclick="closeNotifPanel()"></div>

        <!-- Notification Slide Panel -->
        <div class="notif-panel" id="notifPanel">
            <div style="padding:16px;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;">
                <div>
                    <h2 style="margin:0;font-size:17px;font-weight:700;color:#111827;">Notifications</h2>
                    <p style="margin:2px 0 0;font-size:12px;color:#6b7280;" id="notifSubtitle">Loading…</p>
                </div>
                <div style="display:flex;gap:8px;align-items:center;">
                    <button onclick="markAllRead()" style="font-size:12px;color:#7c3aed;font-weight:600;background:none;border:none;cursor:pointer;padding:6px 10px;border-radius:8px;" id="markAllBtn">Mark all read</button>
                    <button onclick="closeNotifPanel()" style="width:32px;height:32px;border-radius:8px;background:#f3f4f6;border:none;display:flex;align-items:center;justify-content:center;cursor:pointer;">
                        <svg width="16" height="16" fill="none" stroke="#6b7280" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
            <div style="flex:1;overflow-y:auto;" id="notifList">
                <div style="padding:40px 16px;text-align:center;color:#9ca3af;">
                    <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;opacity:0.4;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <p style="font-size:14px;">Loading notifications…</p>
                </div>
            </div>
            <div style="padding:12px 16px;border-top:1px solid #e5e7eb;flex-shrink:0;">
                <a href="{{ route('student.notifications.index') }}" style="display:block;text-align:center;padding:12px;background:#f3e8ff;color:#7c3aed;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;">View All Notifications</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-area">
            @yield('mobile-content')
        </div>

        <!-- Bottom Navigation -->
        <nav class="bottom-nav">
            <div class="flex">
                <a href="{{ route('student.dashboard') }}" class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Home</span>
                </a>
                <a href="{{ route('student.courses') }}" class="nav-item {{ request()->routeIs('student.courses') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span>Courses</span>
                </a>
                <a href="{{ route('student.live_classes.index') }}" class="nav-item {{ request()->routeIs('student.live_classes.*') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span>Live</span>
                </a>
                <button type="button" onclick="openNotifPanel()" class="nav-item {{ request()->routeIs('student.notifications.*') ? 'active' : '' }}" style="border:none;background:none;flex:1;">
                    <div class="notif-bell" style="position:relative;display:inline-block;">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <span class="notif-badge" id="bellBadge" style="display:none;">0</span>
                    </div>
                    <span>Alerts</span>
                </button>
                <button type="button" onclick="openProfileMenu()" class="nav-item" style="border:none;background:none;flex:1;">
                    <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>More</span>
                </button>
            </div>
        </nav>

        <!-- Profile Menu Overlay -->
        <div id="profileOverlay" class="notif-overlay" onclick="closeProfileMenu()"></div>

        <!-- Profile Menu Panel -->
        <div id="profilePanel" class="notif-panel" style="width: 280px; right: 0; left: auto;">
            <div class="notif-header" style="padding: 16px 20px; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-size: 18px; font-weight: 700; color: #1f2937;">Menu</h3>
                <button onclick="closeProfileMenu()" style="background: none; border: none; padding: 4px; cursor: pointer;">
                    <svg width="24" height="24" fill="none" stroke="#6b7280" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="notif-list" id="profileMenuList">
                <!-- Download APK Option - ALWAYS VISIBLE -->
                <a href="{{ url('/student/download-app') }}" class="notif-item" style="text-decoration: none; color: inherit; display: flex !important;">
                    <div class="notif-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title" style="color: #059669; font-weight: 700;">Download App</div>
                        <div class="notif-desc">Get Android APK</div>
                    </div>
                </a>

                <!-- Install App Option (PWA) - Hidden if already installed -->
                <div id="installAppOption" class="notif-item" onclick="installApp()" style="cursor: pointer;">
                    <div class="notif-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">Add to Home Screen</div>
                        <div class="notif-desc">Quick install (no download)</div>
                    </div>
                </div>

                <a href="{{ route('student.about') }}" class="notif-item" style="text-decoration: none; color: inherit;">
                    <div class="notif-icon" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
                        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">About Us</div>
                        <div class="notif-desc">Know about coaching</div>
                    </div>
                </a>

                <a href="{{ route('student.profile') }}" class="notif-item" style="text-decoration: none; color: inherit;">
                    <div class="notif-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">My Profile</div>
                        <div class="notif-desc">Manage account</div>
                    </div>
                </a>

                <a href="{{ route('student.payments.index') }}" class="notif-item" style="text-decoration: none; color: inherit;">
                    <div class="notif-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="notif-content">
                        <div class="notif-title">Payments</div>
                        <div class="notif-desc">View payment history</div>
                    </div>
                </a>

                <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                    @csrf
                    <button type="submit" class="notif-item" style="width: 100%; border: none; background: none; text-align: left; cursor: pointer;">
                        <div class="notif-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            <svg width="20" height="20" fill="none" stroke="white" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                        </div>
                        <div class="notif-content">
                            <div class="notif-title" style="color: #dc2626;">Logout</div>
                            <div class="notif-desc">Sign out of your account</div>
                        </div>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content
                 || '{{ csrf_token() }}';

    // Poll unread count every 60s
    function fetchBadge() {
        fetch('{{ route("student.notifications.unread_count") }}')
            .then(r => r.json())
            .then(data => {
                const badge = document.getElementById('bellBadge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = 'flex';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }).catch(() => {});
    }
    fetchBadge();
    setInterval(fetchBadge, 60000);

    function openNotifPanel() {
        document.getElementById('notifPanel').classList.add('open');
        document.getElementById('notifOverlay').classList.add('open');
        loadNotifications();
    }

    function closeNotifPanel() {
        document.getElementById('notifPanel').classList.remove('open');
        document.getElementById('notifOverlay').classList.remove('open');
        fetchBadge();
    }

    function loadNotifications() {
        fetch('{{ route("student.notifications.recent") }}')
            .then(r => r.json())
            .then(data => {
                const list = document.getElementById('notifList');
                const subtitle = document.getElementById('notifSubtitle');
                const notifications = data.notifications;
                const unread = notifications.filter(n => !n.is_read).length;
                subtitle.textContent = unread > 0 ? `${unread} unread` : 'All caught up!';

                if (notifications.length === 0) {
                    list.innerHTML = `<div style="padding:48px 16px;text-align:center;color:#9ca3af;">
                        <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin:0 auto 12px;display:block;opacity:0.3;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <p style="font-size:15px;font-weight:600;margin:0 0 4px;">No notifications yet</p>
                        <p style="font-size:13px;margin:0;">You'll see updates here</p>
                    </div>`;
                    return;
                }

                list.innerHTML = notifications.map(n => `
                    <div class="notif-item ${n.is_read ? '' : 'unread'}" onclick="handleNotifClick(${n.id}, '${n.url || ''}')">
                        <div class="notif-icon ${n.icon_class}">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">${n.icon_svg}</svg>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="margin:0 0 2px;font-size:13px;font-weight:${n.is_read ? '500' : '700'};color:#111827;line-height:1.4;">${n.title}</p>
                            ${n.body ? `<p style="margin:0 0 4px;font-size:12px;color:#6b7280;line-height:1.4;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">${n.body}</p>` : ''}
                            <p style="margin:0;font-size:11px;color:#9ca3af;">${n.time}</p>
                        </div>
                        ${!n.is_read ? '<div style="width:8px;height:8px;border-radius:50%;background:#7c3aed;flex-shrink:0;margin-top:4px;"></div>' : ''}
                    </div>`).join('');
            }).catch(() => {
                document.getElementById('notifList').innerHTML = '<div style="padding:32px;text-align:center;color:#9ca3af;font-size:13px;">Failed to load. Tap to retry.</div>';
            });
    }

    function handleNotifClick(id, url) {
        fetch(`/student/notifications/${id}/read`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
        }).then(() => {
            fetchBadge();
            if (url) { window.location.href = url; }
            else { loadNotifications(); }
        });
    }

    function markAllRead() {
        fetch('{{ route("student.notifications.read_all") }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json' }
        }).then(() => { loadNotifications(); fetchBadge(); });
    }

    /* ========== PWA Install Logic ========== */
    let deferredPrompt = null;
    const installBanner = document.getElementById('pwaInstallBanner');
    const iosHint = document.getElementById('pwaIosHint');
    const installBtn = document.getElementById('pwaInstallBtn');
    const closeBtn = document.getElementById('pwaCloseBtn');
    const iosCloseBtn = document.getElementById('pwaIosCloseBtn');

    // Check if already installed
    function isPWAInstalled() {
        // Check if running as standalone (installed PWA)
        if (window.matchMedia('(display-mode: standalone)').matches) {
            return true;
        }
        // For iOS
        if (window.navigator.standalone === true) {
            return true;
        }
        // Check localStorage flag
        if (localStorage.getItem('pwa-installed') === 'true') {
            return true;
        }
        return false;
    }

    // Check if user dismissed the banner
    function wasBannerDismissed() {
        const dismissed = localStorage.getItem('pwa-banner-dismissed');
        if (!dismissed) return false;
        
        // Reset dismissal after 7 days
        const dismissedDate = new Date(dismissed);
        const now = new Date();
        const daysDiff = (now - dismissedDate) / (1000 * 60 * 60 * 24);
        
        if (daysDiff > 7) {
            localStorage.removeItem('pwa-banner-dismissed');
            return false;
        }
        return true;
    }

    // Show install banner for all browsers
    function showInstallBanner() {
        if (installBanner && !isPWAInstalled() && !wasBannerDismissed()) {
            installBanner.classList.add('show');
        }
    }

    // Show iOS hint
    function showIosHint() {
        if (iosHint && !wasBannerDismissed()) {
            iosHint.classList.add('show');
        }
    }

    // Hide banners
    function hideInstallBanner() {
        if (installBanner) installBanner.classList.remove('show');
    }

    function hideIosHint() {
        if (iosHint) iosHint.classList.remove('show');
    }

    // Register service worker (skip on insecure contexts)
    if ('serviceWorker' in navigator && location.protocol === 'https:' && !location.hostname.includes('localhost')) {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('SW registered:', reg))
            .catch(err => console.log('SW registration failed:', err));
    }

    // Listen for install prompt (Chrome/Android/Desktop)
    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome from showing its own mini-infobar
        e.preventDefault();

        // Store the event for later use
        deferredPrompt = e;

        // Update install button text
        if (installBtn) {
            installBtn.textContent = 'Install';
        }
    });

    // Show banner for all browsers after a delay
    setTimeout(() => {
        if (!isPWAInstalled() && !wasBannerDismissed() && !isIOS()) {
            showInstallBanner();
        }
    }, 3000); // Show after 3 seconds

    // Install button click handler
    if (installBtn) {
        installBtn.addEventListener('click', async () => {
            if (deferredPrompt) {
                // Chrome/Edge - use native install prompt
                deferredPrompt.prompt();

                // Wait for user choice
                const { outcome } = await deferredPrompt.userChoice;

                if (outcome === 'accepted') {
                    localStorage.setItem('pwa-installed', 'true');
                    hideInstallBanner();
                }

                // Clear the deferred prompt variable
                deferredPrompt = null;
            } else {
                // Other browsers - show instructions modal
                showInstructionsModal();
            }
        });
    }

    // Instructions modal functions
    function showInstructionsModal() {
        const modal = document.getElementById('pwaInstructionsModal');
        if (modal) {
            modal.classList.add('show');
        }
    }

    function hideInstructionsModal() {
        const modal = document.getElementById('pwaInstructionsModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }

    // Close modal on backdrop click
    document.getElementById('pwaInstructionsModal').addEventListener('click', (e) => {
        if (e.target.id === 'pwaInstructionsModal') {
            hideInstructionsModal();
        }
    });

    // Close button handlers
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            hideInstallBanner();
            localStorage.setItem('pwa-banner-dismissed', new Date().toISOString());
        });
    }

    if (iosCloseBtn) {
        iosCloseBtn.addEventListener('click', () => {
            hideIosHint();
            localStorage.setItem('pwa-banner-dismissed', new Date().toISOString());
        });
    }

    // Handle successful installation
    window.addEventListener('appinstalled', () => {
        localStorage.setItem('pwa-installed', 'true');
        hideInstallBanner();
        hideIosHint();
        deferredPrompt = null;
    });

    // iOS detection and hint
    function isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    }

    function isSafari() {
        return /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
    }

    // Show iOS hint if applicable
    if (isIOS() && isSafari() && !isPWAInstalled() && !wasBannerDismissed()) {
        setTimeout(() => {
            showIosHint();
        }, 3000); // Show after 3 seconds
    }

    // ========== Profile Menu Functions ==========
    function openProfileMenu() {
        document.getElementById('profilePanel').classList.add('open');
        document.getElementById('profileOverlay').classList.add('open');

        // Show/hide install option based on installation status
        const installOption = document.getElementById('installAppOption');
        if (installOption) {
            if (isPWAInstalled()) {
                installOption.style.display = 'none';
            } else {
                installOption.style.display = 'flex';
            }
        }
    }

    function closeProfileMenu() {
        document.getElementById('profilePanel').classList.remove('open');
        document.getElementById('profileOverlay').classList.remove('open');
    }

    // Install App from menu
    async function installApp() {
        closeProfileMenu();

        if (deferredPrompt) {
            // Chrome/Edge - use native install prompt
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;

            if (outcome === 'accepted') {
                localStorage.setItem('pwa-installed', 'true');
                hideInstallBanner();
                // Hide install option
                const installOption = document.getElementById('installAppOption');
                if (installOption) installOption.style.display = 'none';
            }
            deferredPrompt = null;
        } else if (isIOS()) {
            // iOS Safari - cannot programmatically install; show clear instructions
            alert('To install this app on iPhone/iPad:\n\n1. Tap the Share button (rectangle with arrow) at the bottom of Safari\n2. Scroll down and tap "Add to Home Screen"\n3. Tap "Add" in the top right\n\nThe app will then appear on your home screen!');
            showIosHint();
        } else {
            // Android non-Chrome or other browsers
            if (/Android/.test(navigator.userAgent)) {
                alert('To install this app:\n\n1. Open this page in Google Chrome browser\n2. Tap the menu (3 dots) → "Add to Home screen"\n3. Tap "Install"\n\nOr use Chrome to get the one-tap install prompt.');
            } else {
                showInstructionsModal();
            }
        }
    }

    // Hide install option if already installed
    if (isPWAInstalled()) {
        const installOption = document.getElementById('installAppOption');
        if (installOption) installOption.style.display = 'none';
    }
    </script>
    @stack('scripts')
</body>
</html>
