<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BTLiveSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'btlive_sessions';

    protected $fillable = [
        'tenant_id',
        'course_id',
        'subject_id',
        'chapter_id',
        'lesson_id',
        'live_class_id',
        'teacher_id',
        'title',
        'description',
        'room_name',
        'access_code',
        'status', // 'scheduled', 'live', 'paused', 'ended'
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration_minutes',
        'max_participants',
        'settings',
        // Feature flags
        'chat_enabled',
        'raise_hand_enabled',
        'polls_enabled',
        'whiteboard_enabled',
        'pdf_enabled',
        'recording_enabled',
        'replay_enabled',
        // Current state
        'current_pdf_id',
        'current_pdf_page',
        'current_whiteboard_data',
        'participant_count',
    ];

    protected $casts = [
        'settings' => 'json',
        'current_whiteboard_data' => 'json',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'chat_enabled' => 'boolean',
        'raise_hand_enabled' => 'boolean',
        'polls_enabled' => 'boolean',
        'whiteboard_enabled' => 'boolean',
        'pdf_enabled' => 'boolean',
        'recording_enabled' => 'boolean',
        'replay_enabled' => 'boolean',
        'participant_count' => 'integer',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function chapter()
    {
        return $this->belongsTo(Chapter::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function liveClass()
    {
        return $this->belongsTo(LiveClass::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function participants()
    {
        return $this->hasMany(BTLiveParticipant::class, 'session_id');
    }

    public function pdfs()
    {
        return $this->hasMany(BTLivePdf::class, 'session_id');
    }

    public function whiteboardEvents()
    {
        return $this->hasMany(BTLiveWhiteboardEvent::class, 'session_id');
    }

    public function polls()
    {
        return $this->hasMany(BTLivePoll::class, 'session_id');
    }

    public function chatMessages()
    {
        return $this->hasMany(BTLiveChatMessage::class, 'session_id');
    }

    public function raisedHands()
    {
        return $this->hasMany(BTLiveRaisedHand::class, 'session_id');
    }

    public function recording()
    {
        return $this->hasOne(BTLiveRecording::class, 'session_id');
    }

    public function replayTimeline()
    {
        return $this->hasMany(BTLiveReplayTimeline::class, 'session_id')->orderBy('timestamp');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'live');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    // Helpers
    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function isEnded(): bool
    {
        return $this->status === 'ended';
    }

    public function hasRecording(): bool
    {
        return $this->recording()->exists();
    }
}
