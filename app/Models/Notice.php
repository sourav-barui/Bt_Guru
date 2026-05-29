<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'created_by',
        'title',
        'content',
        'type',
        'audience',
        'course_id',
        'publish_at',
        'expire_at',
        'is_active',
    ];

    protected $casts = [
        'publish_at' => 'datetime',
        'expire_at' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'type' => 'general',
        'audience' => 'all',
        'is_active' => true,
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expire_at')
                    ->orWhere('expire_at', '>=', now());
            });
    }

    public function scopeForStudents($query)
    {
        return $query->whereIn('audience', ['all', 'students']);
    }

    public function scopeForTeachers($query)
    {
        return $query->whereIn('audience', ['all', 'teachers']);
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where(function ($q) use ($courseId) {
            $q->whereNull('course_id')
                ->orWhere('course_id', $courseId);
        });
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match($this->type) {
            'urgent' => 'danger',
            'important' => 'warning',
            'general' => 'info',
            default => 'secondary',
        };
    }

    public function isPublished(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->publish_at && $this->publish_at->isFuture()) {
            return false;
        }

        if ($this->expire_at && $this->expire_at->isPast()) {
            return false;
        }

        return true;
    }

    public function getExcerptAttribute(): string
    {
        return str()->limit(strip_tags($this->content ?? ''), 100);
    }

    public function getIsPublishedAttribute(): bool
    {
        return $this->isPublished();
    }
}
