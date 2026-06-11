<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BTLiveRaisedHand extends Model
{
    use HasFactory;

    protected $table = 'btlive_raised_hands';

    protected $fillable = [
        'session_id',
        'participant_id',
        'status', // 'raised', 'accepted', 'rejected', 'lowered'
        'raised_at',
        'accepted_at',
        'accepted_by', // teacher who accepted
        'rejected_at',
        'rejected_by',
        'lowered_at',
        'reason', // optional reason for raising hand
        'timestamp', // relative to session start (ms)
    ];

    protected $casts = [
        'raised_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'lowered_at' => 'datetime',
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

    public function accepter()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'raised');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['raised', 'accepted']);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('raised_at');
    }

    // Helpers
    public function accept(int $teacherId)
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'accepted_by' => $teacherId,
        ]);
    }

    public function reject(int $teacherId)
    {
        $this->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => $teacherId,
        ]);
    }

    public function lower()
    {
        $this->update([
            'status' => 'lowered',
            'lowered_at' => now(),
        ]);
    }

    public function isPending(): bool
    {
        return $this->status === 'raised';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function getWaitTimeAttribute(): int
    {
        if (!$this->raised_at) {
            return 0;
        }
        $end = $this->accepted_at ?? $this->rejected_at ?? $this->lowered_at ?? now();
        return $this->raised_at->diffInSeconds($end);
    }
}
