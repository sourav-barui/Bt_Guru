<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BTLivePoll extends Model
{
    use HasFactory;

    protected $table = 'btlive_polls';

    protected $fillable = [
        'session_id',
        'created_by',
        'question',
        'options', // JSON array of option strings
        'correct_option_index', // null for opinion polls, 0-based index for quizzes
        'is_multiple_choice',
        'is_anonymous',
        'status', // 'draft', 'active', 'closed', 'revealed'
        'started_at',
        'ended_at',
        'duration_seconds', // auto-close after X seconds
        'show_results_to_students',
        'timestamp', // relative to session start (ms)
    ];

    protected $casts = [
        'options' => 'json',
        'is_multiple_choice' => 'boolean',
        'is_anonymous' => 'boolean',
        'show_results_to_students' => 'boolean',
        'correct_option_index' => 'integer',
        'duration_seconds' => 'integer',
        'timestamp' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(BTLiveSession::class, 'session_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function answers()
    {
        return $this->hasMany(BTLivePollAnswer::class, 'poll_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    // Helpers
    public function start()
    {
        $this->update([
            'status' => 'active',
            'started_at' => now(),
        ]);
    }

    public function close()
    {
        $this->update([
            'status' => 'closed',
            'ended_at' => now(),
        ]);
    }

    public function revealResults()
    {
        $this->update(['status' => 'revealed']);
    }

    public function getResults(): array
    {
        $totalAnswers = $this->answers()->count();
        $optionCounts = [];

        foreach ($this->options as $index => $option) {
            $count = $this->answers()->where('option_index', $index)->count();
            $optionCounts[] = [
                'index' => $index,
                'text' => $option,
                'count' => $count,
                'percentage' => $totalAnswers > 0 ? round(($count / $totalAnswers) * 100, 1) : 0,
            ];
        }

        return [
            'total_votes' => $totalAnswers,
            'options' => $optionCounts,
            'correct_index' => $this->correct_option_index,
        ];
    }

    public function hasParticipantAnswered(int $participantId): bool
    {
        return $this->answers()->where('participant_id', $participantId)->exists();
    }

    public function getParticipantAnswer(int $participantId): ?BTLivePollAnswer
    {
        return $this->answers()->where('participant_id', $participantId)->first();
    }
}
