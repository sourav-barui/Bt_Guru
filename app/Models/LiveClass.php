<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
// Relationships resolved via Eloquent – no explicit use needed for same namespace

class LiveClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'course_id',
        'subject_id',
        'chapter_id',
        'lesson_id',
        'created_by',
        'title',
        'description',
        'platform',
        'meeting_url',
        'video_url',
        'meeting_id',
        'meeting_password',
        'scheduled_at',
        'duration_minutes',
        'status',
        'recurrence',
        'is_public',
        // BTLive fields
        'is_btlive',
        'btlive_room_name',
        'btlive_room_password',
        'btlive_recording_id',
        'btlive_recording_url',
        'btlive_recording_status',
        'btlive_lobby_enabled',
        'btlive_waiting_room_enabled',
        'btlive_chat_enabled',
        'btlive_teacher_only_video',
        'btlive_teacher_only_audio',
        'btlive_attendance_enabled',
        'btlive_jwt_required',
        'btlive_started_at',
        'btlive_ended_at',
    ];

    protected $casts = [
        'scheduled_at'     => 'datetime',
        'duration_minutes' => 'integer',
        'is_public'        => 'boolean',
        // BTLive casts
        'is_btlive' => 'boolean',
        'btlive_lobby_enabled' => 'boolean',
        'btlive_waiting_room_enabled' => 'boolean',
        'btlive_chat_enabled' => 'boolean',
        'btlive_teacher_only_video' => 'boolean',
        'btlive_teacher_only_audio' => 'boolean',
        'btlive_attendance_enabled' => 'boolean',
        'btlive_jwt_required' => 'boolean',
        'btlive_started_at' => 'datetime',
        'btlive_ended_at' => 'datetime',
    ];

    public function tenant(): BelongsTo   { return $this->belongsTo(Tenant::class); }
    public function course(): BelongsTo   { return $this->belongsTo(Course::class); }
    public function subject(): BelongsTo  { return $this->belongsTo(Subject::class); }
    public function chapter(): BelongsTo  { return $this->belongsTo(Chapter::class); }
    public function lesson(): BelongsTo   { return $this->belongsTo(Lesson::class); }
    public function creator(): BelongsTo  { return $this->belongsTo(User::class, 'created_by'); }

    // BTLive attendance
    public function attendance(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\LiveClassAttendance::class)->orderBy('joined_at', 'desc');
    }

    public function getLevelLabelAttribute(): string
    {
        if ($this->lesson_id)  return 'Lesson';
        if ($this->chapter_id) return 'Chapter';
        if ($this->subject_id) return 'Subject';
        return 'Course';
    }

    // ── Computed ──────────────────────────────────────────────────

    public function getEndsAtAttribute(): Carbon
    {
        return $this->scheduled_at->copy()->addMinutes($this->duration_minutes);
    }

    public function getIsLiveNowAttribute(): bool
    {
        $now = now();
        return $now->greaterThanOrEqualTo($this->scheduled_at)
            && $now->lessThan($this->ends_at);
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->scheduled_at->isFuture();
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed' || $this->ends_at->isPast();
    }

    public function getPlatformLabelAttribute(): string
    {
        return match($this->platform) {
            'google_meet' => 'Google Meet',
            'zoom'        => 'Zoom',
            'ms_teams'    => 'Microsoft Teams',
            'jitsi'       => 'Jitsi Meet',
            default       => 'Other',
        };
    }

    public function getPlatformColorAttribute(): string
    {
        return match($this->platform) {
            'google_meet' => 'bg-green-100 text-green-700',
            'zoom'        => 'bg-blue-100 text-blue-700',
            'ms_teams'    => 'bg-purple-100 text-purple-700',
            'jitsi'       => 'bg-orange-100 text-orange-700',
            default       => 'bg-gray-100 text-gray-700',
        };
    }

    public function getPlatformIconAttribute(): string
    {
        return match($this->platform) {
            'google_meet' => '🟢',
            'zoom'        => '🔵',
            'ms_teams'    => '🟣',
            'jitsi'       => '🟠',
            default       => '🎥',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->is_live_now) return 'bg-red-100 text-red-700 animate-pulse';
        return match($this->status) {
            'scheduled'  => 'bg-blue-100 text-blue-700',
            'live'       => 'bg-red-100 text-red-700',
            'completed'  => 'bg-gray-100 text-gray-500',
            'cancelled'  => 'bg-red-50 text-red-400',
            default      => 'bg-gray-100 text-gray-500',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->is_live_now) return 'LIVE NOW';
        return match($this->status) {
            'scheduled'  => 'Scheduled',
            'live'       => 'Live',
            'completed'  => 'Ended',
            'cancelled'  => 'Cancelled',
            default      => ucfirst($this->status),
        };
    }

    /**
     * Get meeting URL with password embedded (for auto-join)
     * Different platforms handle passwords differently
     */
    public function getSecureMeetingUrlAttribute(): string
    {
        $url = $this->meeting_url;
        $password = $this->meeting_password;

        if (empty($password)) {
            return $url;
        }

        // Platform-specific password embedding
        return match($this->platform) {
            'zoom' => $this->embedZoomPassword($url, $password),
            'jitsi' => $this->embedJitsiPassword($url, $password),
            'google_meet', 'ms_teams', 'other' => $url, // These handle passwords via waiting room or embedded in original URL
            default => $url,
        };
    }

    /**
     * Embed Zoom password in URL
     * Format: https://zoom.us/j/1234567890?pwd=encryptedPassword or ?password=plain
     */
    private function embedZoomPassword(string $url, string $password): string
    {
        // If URL already has query params, append with &
        $separator = str_contains($url, '?') ? '&' : '?';
        
        // Zoom supports both pwd (encrypted) and password (plain) params
        // Using password param for plain text (browser will pass it)
        return $url . $separator . 'password=' . urlencode($password);
    }

    /**
     * Embed Jitsi password in URL
     * Jitsi uses #config.password= or JWT tokens
     */
    private function embedJitsiPassword(string $url, string $password): string
    {
        // Jitsi can use hash fragment for password
        return $url . '#config.password=' . urlencode($password);
    }

    // ── Scopes ────────────────────────────────────────────────────

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')->where('scheduled_at', '>', now());
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }
}
