<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'description',
        'fees',
        'fees_type',
        'past_month_fee',
        'duration',
        'start_date',
        'end_date',
        'thumbnail',
        'status',
        'metadata',
    ];

    protected $casts = [
        'fees' => 'decimal:2',
        'past_month_fee' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'fees' => 0,
        'fees_type' => 'one_time',
        'past_month_fee' => 0,
        'status' => 'active',
        'metadata' => '[]',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_teacher', 'course_id', 'teacher_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function primaryTeacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
            ->where('enrollment_status', 'active')
            ->withPivot(['payment_status', 'enrollment_status', 'enrolled_at', 'fees_paid'])
            ->withTimestamps();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(CourseSubscription::class);
    }

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class);
    }

    public function curricula(): HasMany
    {
        return $this->hasMany(Curriculum::class)->orderBy('order');
    }

    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class)->orderBy('scheduled_at');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class)->orderBy('created_at', 'desc');
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'active');
    }

    public function getEnrolledStudentsCountAttribute(): int
    {
        return $this->enrollments()->where('enrollment_status', 'active')->count();
    }

    public function getTotalFeesCollectedAttribute(): float
    {
        return $this->enrollments()->where('payment_status', 'completed')->sum('fees_paid');
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        return asset('images/default-course.png');
    }

    protected static function booted(): void
    {
        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });

        static::updating(function ($course) {
            if ($course->isDirty('title') && empty($course->slug)) {
                $course->slug = Str::slug($course->title);
            }
        });
    }
}
