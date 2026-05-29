<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Subscription extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'start_date',
        'end_date',
        'trial_end_date',
        'status',
        'coupon_code_used',
        'original_price',
        'discount_amount',
        'final_price',
        'payment_status',
        'payment_method',
        'payment_id',
        'paid_at',
        'auto_renew',
        'cancelled_at',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'trial_end_date' => 'date',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'auto_renew' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeTrial($query)
    {
        return $query->where('status', 'trial');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePendingPayment($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeExpiringSoon($query, int $days = 7)
    {
        return $query->where('status', 'active')
            ->where('end_date', '<=', Carbon::now()->addDays($days))
            ->where('end_date', '>', Carbon::now());
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '<', Carbon::now());
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date && $this->end_date->isFuture();
    }

    public function isTrial(): bool
    {
        return $this->status === 'trial' && $this->trial_end_date && $this->trial_end_date->isFuture();
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->end_date && $this->end_date->isPast());
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->end_date) return 0;
        return max(0, Carbon::now()->diffInDays($this->end_date, false));
    }

    public function getTrialDaysRemainingAttribute(): int
    {
        if (!$this->trial_end_date) return 0;
        return max(0, Carbon::now()->diffInDays($this->trial_end_date, false));
    }

    public function markAsPaid(string $paymentMethod = null, string $paymentId = null): void
    {
        $this->update([
            'payment_status' => 'paid',
            'payment_method' => $paymentMethod,
            'payment_id' => $paymentId,
            'paid_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);
    }

    public function activate(): void
    {
        $this->load('plan');
        
        $this->update([
            'status' => 'active',
            'start_date' => $this->start_date ?? now(),
            'end_date' => $this->end_date ?? ($this->plan ? now()->addDays($this->plan->duration_days) : now()->addDays(30)),
        ]);
    }
}
