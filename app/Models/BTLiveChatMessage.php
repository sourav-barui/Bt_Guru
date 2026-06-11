<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BTLiveChatMessage extends Model
{
    use HasFactory;

    protected $table = 'btlive_chat_messages';

    protected $fillable = [
        'session_id',
        'participant_id', // null for system messages
        'message_type', // 'text', 'system', 'teacher', 'file', 'notification'
        'content',
        'file_path', // for file attachments
        'is_pinned',
        'is_deleted',
        'deleted_by',
        'reply_to_id', // for threaded replies
        'timestamp', // relative to session start (ms)
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_deleted' => 'boolean',
        'timestamp' => 'integer',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(BTLiveSession::class, 'session_id');
    }

    public function participant()
    {
        return $this->belongsTo(BTLiveParticipant::class, 'participant_id');
    }

    public function replyTo()
    {
        return $this->belongsTo(self::class, 'reply_to_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'reply_to_id');
    }

    // Scopes
    public function scopeVisible($query)
    {
        return $query->where('is_deleted', false);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    public function scopeFromTimestamp($query, int $timestamp)
    {
        return $query->where('timestamp', '>=', $timestamp);
    }

    // Helpers
    public function pin()
    {
        $this->update(['is_pinned' => true]);
    }

    public function unpin()
    {
        $this->update(['is_pinned' => false]);
    }

    public function softDelete(int $deletedBy)
    {
        $this->update([
            'is_deleted' => true,
            'deleted_by' => $deletedBy,
        ]);
    }

    public static function addSystemMessage(int $sessionId, string $content, int $timestamp = null): self
    {
        return self::create([
            'session_id' => $sessionId,
            'participant_id' => null,
            'message_type' => 'system',
            'content' => $content,
            'timestamp' => $timestamp ?? self::getCurrentSessionTimestamp($sessionId),
        ]);
    }

    private static function getCurrentSessionTimestamp(int $sessionId): int
    {
        $session = BTLiveSession::find($sessionId);
        if (!$session || !$session->started_at) {
            return 0;
        }
        return now()->diffInMilliseconds($session->started_at);
    }
}
