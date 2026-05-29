<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CourseSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'enrollment_id',
        'student_id',
        'course_id',
        'access_start',
        'access_end',
        'type',
        'fee_paid',
        'payment_status',
        'remarks',
        'created_by',
    ];

    protected $casts = [
        'access_start' => 'datetime',
        'access_end'   => 'datetime',
        'fee_paid'     => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->payment_status === 'paid'
            && $now->between($this->access_start, $this->access_end);
    }

    public function isPast(): bool
    {
        return $this->type === 'past';
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('payment_status', 'paid')
            ->where('access_start', '<=', $now)
            ->where('access_end', '>=', $now);
    }

    public function getMonthLabelAttribute(): string
    {
        return $this->access_start->format('M Y');
    }

    public function getAccessRangeAttribute(): string
    {
        return $this->access_start->format('d M Y') . ' – ' . $this->access_end->format('d M Y');
    }
}
