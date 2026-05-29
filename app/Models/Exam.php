<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
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
        'template',
        'status',
        'total_marks',
        'passing_marks',
        'duration_minutes',
        'total_questions',
        'shuffle_questions',
        'show_result_immediately',
        'allow_multiple_attempts',
        'max_attempts',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'shuffle_questions' => 'boolean',
        'show_result_immediately' => 'boolean',
        'allow_multiple_attempts' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sections()
    {
        return $this->hasMany(ExamSection::class)->orderBy('order');
    }

    public function questions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForLevel($query, $level, $levelId)
    {
        return $query->where($level . '_id', $levelId);
    }

    // Get level name
    public function getLevelNameAttribute()
    {
        if ($this->lesson_id) return 'Lesson: ' . $this->lesson?->title;
        if ($this->chapter_id) return 'Chapter: ' . $this->chapter?->title;
        if ($this->subject_id) return 'Subject: ' . $this->subject?->title;
        return 'Course Level';
    }

    // Get level type
    public function getLevelTypeAttribute()
    {
        if ($this->lesson_id) return 'lesson';
        if ($this->chapter_id) return 'chapter';
        if ($this->subject_id) return 'subject';
        return 'course';
    }

    // Status badges
    public function getStatusBadgeAttribute()
    {
        return [
            'draft' => 'bg-gray-100 text-gray-800',
            'published' => 'bg-blue-100 text-blue-800',
            'active' => 'bg-green-100 text-green-800',
            'completed' => 'bg-purple-100 text-purple-800',
            'archived' => 'bg-red-100 text-red-800',
        ][$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    // Calculate total marks from questions
    public function recalculateTotalMarks()
    {
        $this->total_marks = $this->questions()->sum('marks');
        $this->total_questions = $this->questions()->count();
        $this->save();
    }
}
