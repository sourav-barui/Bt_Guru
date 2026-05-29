<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'user_id',
        'enrollment_id',
        'started_at',
        'submitted_at',
        'ended_at',
        'total_questions',
        'answered_count',
        'correct_count',
        'wrong_count',
        'skipped_count',
        'marks_obtained',
        'negative_marks',
        'total_marks',
        'percentage',
        'status',
        'is_passed',
        'attempt_number',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'ended_at' => 'datetime',
        'marks_obtained' => 'decimal:2',
        'negative_marks' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'is_passed' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }

    // Scope for active attempts
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    // Calculate score
    public function calculateScore()
    {
        $this->marks_obtained = $this->answers->sum('marks_obtained');
        $this->negative_marks = $this->answers->sum('negative_marks');
        $this->total_marks = $this->marks_obtained - $this->negative_marks;
        
        if ($this->exam && $this->exam->total_marks > 0) {
            $this->percentage = ($this->total_marks / $this->exam->total_marks) * 100;
        }
        
        $this->is_passed = $this->total_marks >= $this->exam->passing_marks;
        $this->save();
    }

    // Get time remaining in seconds
    public function getTimeRemainingAttribute()
    {
        if (!$this->exam->duration_minutes) {
            return null;
        }
        
        $elapsed = (int) now()->diffInSeconds($this->started_at);
        $total = $this->exam->duration_minutes * 60;
        $remaining = $total - $elapsed;
        
        return max(0, $remaining);
    }

    // Check if time has expired
    public function hasTimeExpired()
    {
        return $this->exam->duration_minutes && $this->getTimeRemainingAttribute() <= 0;
    }
}
