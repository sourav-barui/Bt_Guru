<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'course_id',
        'payment_status',
        'enrollment_status',
        'fees_paid',
        'fees_total',
        'enrolled_at',
        'approved_at',
        'approved_by',
        'metadata',
        'remarks',
    ];

    protected $casts = [
        'fees_paid' => 'decimal:2',
        'fees_total' => 'decimal:2',
        'enrolled_at' => 'datetime',
        'approved_at' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'payment_status' => 'pending',
        'enrollment_status' => 'pending',
        'fees_paid' => 0,
        'metadata' => '[]',
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(CourseSubscription::class);
    }

    public function paymentRequests(): HasMany
    {
        return $this->hasMany(\App\Models\PaymentRequest::class);
    }

    public function monthlyFees(): HasMany
    {
        return $this->hasMany(MonthlyFee::class)->orderBy('year')->orderBy('month');
    }

    public function pendingMonthlyFees(): HasMany
    {
        return $this->monthlyFees()->whereIn('status', ['pending', 'overdue']);
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopePending($query)
    {
        return $query->where('enrollment_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('enrollment_status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->where('enrollment_status', 'active');
    }

    public function scopePaymentPending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopePaymentCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function isPending(): bool
    {
        return $this->enrollment_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->enrollment_status === 'approved';
    }

    public function isActive(): bool
    {
        return $this->enrollment_status === 'active';
    }

    public function isPaymentCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    public function getBalanceAmountAttribute(): float
    {
        return max(0, $this->fees_total - $this->fees_paid);
    }

    public function getPaymentPercentageAttribute(): float
    {
        if ($this->fees_total <= 0) {
            return 100;
        }
        return min(100, ($this->fees_paid / $this->fees_total) * 100);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->enrollment_status) {
            'active' => 'success',
            'approved' => 'info',
            'pending' => 'warning',
            'rejected' => 'danger',
            'dropped' => 'secondary',
            'completed' => 'primary',
            default => 'secondary',
        };
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match($this->payment_status) {
            'completed' => 'success',
            'partial' => 'info',
            'pending' => 'warning',
            'refunded' => 'danger',
            default => 'secondary',
        };
    }

    public function markAsApproved(int $approvedBy): void
    {
        $this->update([
            'enrollment_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approvedBy,
        ]);
    }

    public function markAsActive(): void
    {
        $this->update([
            'enrollment_status' => 'active',
            'enrolled_at' => now(),
        ]);
    }

    public function markAsRejected(): void
    {
        $this->update([
            'enrollment_status' => 'rejected',
        ]);
    }

    public function markPaymentCompleted(): void
    {
        $this->update([
            'payment_status' => 'completed',
            'fees_paid' => $this->fees_total,
        ]);
    }

    public function addPayment(float $amount): void
    {
        $newPaid = $this->fees_paid + $amount;
        $status = $newPaid >= $this->fees_total ? 'completed' : 'partial';
        
        $this->update([
            'fees_paid' => $newPaid,
            'payment_status' => $status,
        ]);
    }
}
