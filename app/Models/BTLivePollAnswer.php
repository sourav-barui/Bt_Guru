<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BTLivePollAnswer extends Model
{
    use HasFactory;

    protected $table = 'btlive_poll_answers';

    protected $fillable = [
        'poll_id',
        'participant_id',
        'option_index',
        'answered_at',
        'timestamp', // relative to session start (ms)
    ];

    protected $casts = [
        'option_index' => 'integer',
        'timestamp' => 'integer',
        'answered_at' => 'datetime',
    ];

    // Relationships
    public function poll()
    {
        return $this->belongsTo(BTLivePoll::class, 'poll_id');
    }

    public function participant()
    {
        return $this->belongsTo(BTLiveParticipant::class, 'participant_id');
    }

    // Scopes
    public function scopeForOption($query, int $optionIndex)
    {
        return $query->where('option_index', $optionIndex);
    }
}
