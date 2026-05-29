<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Coupon extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'applicable_plan_ids',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'applicable_plan_ids' => 'array',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'coupon_plan', 'coupon_id', 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            });
    }

    public function scopeValidForPlan($query, int $planId)
    {
        return $query->where(function ($q) use ($planId) {
            $q->whereNull('applicable_plan_ids')
                ->orWhereJsonContains('applicable_plan_ids', $planId);
        });
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
        });
    }

    public function scopeHasUsesRemaining($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('max_uses')->orWhereColumn('used_count', '<', 'max_uses');
        });
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->valid_from && $this->valid_from->isFuture()) return false;
        if ($this->valid_until && $this->valid_until->isPast()) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        return true;
    }

    public function isApplicableToPlan(int $planId): bool
    {
        if (empty($this->applicable_plan_ids)) return true;
        return in_array($planId, $this->applicable_plan_ids);
    }

    public function calculateDiscount(float $originalPrice): float
    {
        if ($this->discount_type === 'percentage') {
            return ($originalPrice * $this->discount_value) / 100;
        }
        return min($this->discount_value, $originalPrice);
    }

    public function apply(float $originalPrice): array
    {
        $discount = $this->calculateDiscount($originalPrice);
        $finalPrice = max(0, $originalPrice - $discount);

        return [
            'original_price' => $originalPrice,
            'discount_amount' => $discount,
            'final_price' => $finalPrice,
            'discount_type' => $this->discount_type,
            'discount_value' => $this->discount_value,
        ];
    }

    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    public function getCodeUpperAttribute(): string
    {
        return strtoupper($this->code);
    }
}
