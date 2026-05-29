<div class="space-y-5">

    {{-- Title --}}
    <div>
        <label class="form-label">Class Title <span class="text-red-500">*</span></label>
        <input type="text" name="title" class="form-input"
               value="{{ old('title', $liveClass->title ?? '') }}"
               placeholder="e.g. Chapter 3 — Live Doubt Session" required>
    </div>

    {{-- Description --}}
    <div>
        <label class="form-label">Description</label>
        <textarea name="description" rows="2" class="form-input"
                  placeholder="What will be covered in this class?">{{ old('description', $liveClass->description ?? '') }}</textarea>
    </div>

    {{-- Platform --}}
    <div>
        <label class="form-label">Platform <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            @php
                $platforms = [
                    'google_meet' => ['label' => 'Google Meet', 'icon' => '🟢', 'color' => 'border-green-300 bg-green-50'],
                    'zoom'        => ['label' => 'Zoom',        'icon' => '🔵', 'color' => 'border-blue-300 bg-blue-50'],
                    'ms_teams'    => ['label' => 'MS Teams',    'icon' => '🟣', 'color' => 'border-purple-300 bg-purple-50'],
                    'jitsi'       => ['label' => 'Jitsi Meet',  'icon' => '🟠', 'color' => 'border-orange-300 bg-orange-50'],
                    'other'       => ['label' => 'Other',       'icon' => '🎥', 'color' => 'border-gray-300 bg-gray-50'],
                ];
                $currentPlatform = old('platform', $liveClass->platform ?? 'google_meet');
            @endphp
            @foreach($platforms as $val => $p)
            <label class="cursor-pointer">
                <input type="radio" name="platform" value="{{ $val }}" class="sr-only peer"
                       {{ $currentPlatform === $val ? 'checked' : '' }}>
                <div class="flex items-center gap-2 border-2 rounded-xl p-3 text-sm font-semibold
                            border-gray-200 bg-white peer-checked:{{ $p['color'] }} peer-checked:border-opacity-100
                            hover:border-gray-300 transition-all peer-checked:ring-1 peer-checked:ring-offset-0">
                    <span class="text-lg">{{ $p['icon'] }}</span>
                    {{ $p['label'] }}
                </div>
            </label>
            @endforeach
        </div>
    </div>

    {{-- Meeting URL --}}
    <div>
        <label class="form-label">Meeting Link <span class="text-red-500">*</span></label>
        <input type="url" name="meeting_url" class="form-input"
               value="{{ old('meeting_url', $liveClass->meeting_url ?? '') }}"
               placeholder="https://meet.google.com/xxx-yyyy-zzz" required>
        <p class="text-xs text-gray-400 mt-1">Paste the full join URL from your meeting platform</p>
    </div>

    {{-- Meeting ID & Password --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="form-label">Meeting ID</label>
            <input type="text" name="meeting_id" class="form-input"
                   value="{{ old('meeting_id', $liveClass->meeting_id ?? '') }}"
                   placeholder="123 456 7890">
        </div>
        <div>
            <label class="form-label">Password / Passcode</label>
            <input type="text" name="meeting_password" class="form-input"
                   value="{{ old('meeting_password', $liveClass->meeting_password ?? '') }}"
                   placeholder="abc123">
        </div>
    </div>

    {{-- Date & Time + Duration --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="form-label">Date & Time <span class="text-red-500">*</span></label>
            <input type="datetime-local" name="scheduled_at" class="form-input"
                   value="{{ old('scheduled_at', isset($liveClass) ? $liveClass->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                   required>
        </div>
        <div>
            <label class="form-label">Duration (minutes) <span class="text-red-500">*</span></label>
            <input type="number" name="duration_minutes" class="form-input" min="5" max="480"
                   value="{{ old('duration_minutes', $liveClass->duration_minutes ?? 60) }}" required>
        </div>
    </div>

    {{-- Public Class Toggle --}}
    <div class="p-3 bg-indigo-50 border border-indigo-100 rounded-lg">
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" name="is_public" value="1" class="w-5 h-5 text-indigo-600 rounded focus:ring-indigo-500"
                   {{ old('is_public', $liveClass->is_public ?? false) ? 'checked' : '' }}>
            <div>
                <span class="font-semibold text-indigo-800">Public Live Class</span>
                <p class="text-xs text-indigo-600">Visible to all registered students (no enrollment required)</p>
            </div>
        </label>
    </div>

    {{-- Status & Recurrence --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
                @foreach(['scheduled' => 'Scheduled', 'live' => 'Live Now', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
                    <option value="{{ $val }}" {{ old('status', $liveClass->status ?? 'scheduled') === $val ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Recurrence</label>
            <select name="recurrence" class="form-input">
                <option value="none"   {{ old('recurrence', $liveClass->recurrence ?? 'none') === 'none'   ? 'selected' : '' }}>One-time</option>
                <option value="weekly" {{ old('recurrence', $liveClass->recurrence ?? 'none') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                <option value="daily"  {{ old('recurrence', $liveClass->recurrence ?? 'none') === 'daily'  ? 'selected' : '' }}>Daily</option>
            </select>
        </div>
    </div>

</div>
