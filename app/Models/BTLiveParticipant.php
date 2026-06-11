<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BTLiveParticipant extends Model
{
    use HasFactory;

    protected $table = 'btlive_participants';

    protected $fillable = [
        'session_id',
        'user_id', // null for guests
        'student_id', // if authenticated as student
        'role', // 'teacher', 'student', 'moderator', 'guest'
        'name',
        'email',
        'device_info',
        'ip_address',
        'joined_at',
        'left_at',
        'last_activity_at',
        'is_active',
        'is_muted',
        'is_camera_off',
        'is_screen_blocked',
        'permissions',
        'connection_quality',
    ];

    protected $casts = [
        'permissions' => 'json',
        'device_info' => 'json',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'is_active' => 'boolean',
        'is_muted' => 'boolean',
        'is_camera_off' => 'boolean',
        'is_screen_blocked' => 'boolean',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(BTLiveSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function chatMessages()
    {
        return $this->hasMany(BTLiveChatMessage::class, 'participant_id');
    }

    public function pollAnswers()
    {
        return $this->hasMany(BTLivePollAnswer::class, 'participant_id');
    }

    public function raisedHands()
    {
        return $this->hasMany(BTLiveRaisedHand::class, 'participant_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    // Helpers
    public function markAsLeft()
    {
        $this->update([
            'is_active' => false,
            'left_at' => now(),
        ]);
    }

    public function updateActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function getDurationAttribute(): int
    {
        if (!$this->joined_at) {
            return 0;
        }
        $end = $this->left_at ?? now();
        return $this->joined_at->diffInSeconds($end);
    }
}
