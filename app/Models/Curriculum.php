<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Curriculum extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class)->orderBy('order');
    }

    public function contents(): MorphMany
    {
        return $this->morphMany(CurriculumContent::class, 'contentable')->orderBy('order');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(CurriculumNote::class, 'noteable')->orderBy('order');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
