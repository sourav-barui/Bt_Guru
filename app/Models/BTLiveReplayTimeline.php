<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BTLiveReplayTimeline extends Model
{
    use HasFactory;

    protected $table = 'btlive_replay_timeline';

    protected $fillable = [
        'session_id',
        'recording_id',
        'event_type', // 'video_start', 'pdf_open', 'page_change', 'annotation', 'poll_start', 'poll_end', 'hand_raise', 'chat', 'whiteboard_clear'
        'timestamp', // relative to session start (ms)
        'data', // JSON payload specific to event type
        'reference_id', // ID of related record (pdf_id, poll_id, etc.)
    ];

    protected $casts = [
        'data' => 'json',
        'timestamp' => 'integer',
        'reference_id' => 'integer',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(BTLiveSession::class, 'session_id');
    }

    public function recording()
    {
        return $this->belongsTo(BTLiveRecording::class, 'recording_id');
    }

    // Scopes
    public function scopeInOrder($query)
    {
        return $query->orderBy('timestamp');
    }

    public function scopeFromTimestamp($query, int $timestamp)
    {
        return $query->where('timestamp', '>=', $timestamp);
    }

    public function scopeOfType($query, string $type)
    {
        return $query->where('event_type', $type);
    }

    // Helpers
    public static function buildTimeline(int $sessionId): array
    {
        $events = self::where('session_id', $sessionId)
            ->orderBy('timestamp')
            ->get();

        return $events->map(fn($event) => [
            'time' => $event->timestamp,
            'type' => $event->event_type,
            'data' => $event->data,
            'ref' => $event->reference_id,
        ])->toArray();
    }

    public static function addEvent(int $sessionId, string $type, array $data, int $timestamp = null, int $refId = null): self
    {
        return self::create([
            'session_id' => $sessionId,
            'event_type' => $type,
            'data' => $data,
            'timestamp' => $timestamp ?? self::getSessionTimestamp($sessionId),
            'reference_id' => $refId,
        ]);
    }

    private static function getSessionTimestamp(int $sessionId): int
    {
        $session = BTLiveSession::find($sessionId);
        if (!$session || !$session->started_at) {
            return 0;
        }
        return now()->diffInMilliseconds($session->started_at);
    }
}
