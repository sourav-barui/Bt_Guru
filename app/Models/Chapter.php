<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Chapter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class)->orderBy('scheduled_at');
    }

    public function contents(): MorphMany
    {
        return $this->morphMany(CurriculumContent::class, 'contentable')->orderBy('order');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(CurriculumNote::class, 'noteable')->orderBy('order');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class)->orderBy('created_at', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
