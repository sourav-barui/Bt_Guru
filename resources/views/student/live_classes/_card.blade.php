@php
    $platformColors = [
        'google_meet' => 'background:#dcfce7;color:#16a34a;',
        'zoom'        => 'background:#dbeafe;color:#1d4ed8;',
        'ms_teams'    => 'background:#ede9fe;color:#7c3aed;',
        'jitsi'       => 'background:#ffedd5;color:#c2410c;',
        'other'       => 'background:#f3f4f6;color:#4b5563;',
    ];
    $pColor = $platformColors[$lc->platform] ?? 'background:#f3f4f6;color:#4b5563;';
@endphp

<div class="lc-card">
    <div class="lc-card-body">
        {{-- Title row --}}
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:6px;">
            <div>
                <p class="lc-card-title">{{ $lc->title }}</p>
                <p class="lc-card-course">{{ $lc->course->title }}</p>
            </div>
            <span class="lc-badge {{ $mode === 'live' ? 'lc-badge-live' : ($mode === 'past' ? 'lc-badge-done' : 'lc-badge-upcoming') }}">
                @if($mode === 'live')
                    <span style="width:7px;height:7px;border-radius:50%;background:#dc2626;display:inline-block;"></span> LIVE
                @elseif($mode === 'past')
                    Ended
                @else
                    Upcoming
                @endif
            </span>
        </div>

        @if($lc->description)
        <p class="lc-card-desc">{{ $lc->description }}</p>
        @endif

        {{-- Meta info --}}
        <div class="lc-meta">
            <div class="lc-meta-item">
                <svg fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                {{ $lc->scheduled_at->format('D, d M Y') }}
            </div>
            <div class="lc-meta-item">
                <svg fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $lc->scheduled_at->format('h:i A') }} · {{ $lc->duration_minutes }} min
            </div>
            <span class="lc-badge lc-badge-platform" style="{{ $pColor }}">
                {{ $lc->platform_icon }} {{ $lc->platform_label }}
            </span>
            @if($lc->recurrence !== 'none')
            <span class="lc-badge" style="background:#e0e7ff;color:#4338ca;">🔁 {{ ucfirst($lc->recurrence) }}</span>
            @endif
        </div>

        {{-- Meeting ID + Password if provided --}}
        @if($lc->meeting_id || $lc->meeting_password)
        <div style="background:#f8fafc;border-radius:10px;padding:10px 12px;margin-bottom:10px;display:flex;gap:16px;flex-wrap:wrap;">
            @if($lc->meeting_id)
            <div>
                <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.8px;">Meeting ID</p>
                <p style="font-size:13px;font-weight:700;color:#111827;font-family:monospace;">{{ $lc->meeting_id }}</p>
            </div>
            @endif
            @if($lc->meeting_password)
            <div>
                <p style="font-size:10px;font-weight:700;color:#9ca3af;text-transform:uppercase;letter-spacing:.8px;">Passcode</p>
                <p style="font-size:13px;font-weight:700;color:#111827;font-family:monospace;">{{ $lc->meeting_password }}</p>
            </div>
            @endif
        </div>
        @endif

        {{-- Join Button --}}
        @if($mode === 'live')
        <a href="{{ $lc->secure_meeting_url }}" target="_blank" class="join-btn join-btn-live">
            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Join
        </a>
        @elseif($mode === 'upcoming')
        <a href="{{ $lc->secure_meeting_url }}" target="_blank" class="join-btn join-btn-upcoming">
            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            Join
        </a>
        @else
        <div class="join-btn join-btn-done">
            <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Class Ended
        </div>
        @endif

    </div>
</div>
