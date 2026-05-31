<div class="p-5 flex flex-col md:flex-row md:items-center gap-4">

    {{-- Platform icon + info --}}
    <div class="flex items-start gap-4 flex-1 min-w-0">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl flex-shrink-0
            {{ match($lc->platform) {
                'google_meet' => 'bg-green-100',
                'zoom'        => 'bg-blue-100',
                'ms_teams'    => 'bg-purple-100',
                'jitsi'       => 'bg-orange-100',
                default       => 'bg-gray-100',
            } }}">
            {{ $lc->platform_icon }}
        </div>
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <p class="font-semibold text-gray-900">{{ $lc->title }}</p>
                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $lc->status_badge }}">
                    {{ $lc->status_label }}
                </span>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $lc->platform_color }}">
                    {{ $lc->platform_label }}
                </span>
                @if($lc->is_btlive)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-red-100 text-red-700 border border-red-200">
                        🔴 BTLive
                    </span>
                @endif
                @if($lc->recurrence !== 'none')
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700">
                    {{ ucfirst($lc->recurrence) }}
                </span>
                @endif
            </div>
            @if($lc->description)
                <p class="text-sm text-gray-500 truncate">{{ $lc->description }}</p>
            @endif
            <div class="flex flex-wrap items-center gap-3 mt-1.5 text-xs text-gray-400">
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ $lc->scheduled_at->format('D, d M Y') }}
                </span>
                <span class="flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $lc->scheduled_at->format('h:i A') }} · {{ $lc->duration_minutes }} min
                </span>
                @if($lc->meeting_id)
                <span>ID: {{ $lc->meeting_id }}</span>
                @endif
                @if($lc->meeting_password)
                <span>Pass: {{ $lc->meeting_password }}</span>
                @endif
                <span>By {{ $lc->creator->name ?? 'Admin' }}</span>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-2 flex-shrink-0">
        {{-- BTLive Room Button --}}
        @if($lc->is_btlive)
            <a href="{{ route('btlive.teacher_room', $lc) }}" target="_blank"
               class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                BTLive Room
            </a>
        @else
            {{-- Convert to BTLive Button --}}
            <form method="POST" action="{{ route('btlive.convert', $lc) }}" 
                  onsubmit="return confirm('Convert this class to BTLive? Students will join via BTLive instead of external meeting link.')">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Make BTLive
                </button>
            </form>
            
            <a href="{{ $lc->meeting_url }}" target="_blank"
               class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.07A1 1 0 0121 8.845v6.31a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Open
            </a>
        @endif

        @if($lc->status === 'scheduled')
        <form method="POST" action="{{ route('tenant.live_classes.markLive', [$course, $lc]) }}">
            @csrf
            <button class="text-xs font-bold px-3 py-2 rounded-lg bg-red-500 text-white hover:bg-red-600">▶ Go Live</button>
        </form>
        @endif

        @if($lc->status === 'live')
        <form method="POST" action="{{ route('tenant.live_classes.markCompleted', [$course, $lc]) }}">
            @csrf
            <button class="text-xs font-bold px-3 py-2 rounded-lg bg-gray-700 text-white hover:bg-gray-800">■ End</button>
        </form>
        @endif

        <a href="{{ route('tenant.live_classes.edit', [$course, $lc]) }}"
           class="text-xs font-bold px-3 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">Edit</a>

        @if(in_array($lc->status, ['scheduled', 'cancelled']))
        <form method="POST" action="{{ route('tenant.live_classes.destroy', [$course, $lc]) }}"
              onsubmit="return confirm('Delete this class?')">
            @csrf @method('DELETE')
            <button class="text-xs font-bold px-3 py-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100">Delete</button>
        </form>
        @endif
    </div>
</div>
