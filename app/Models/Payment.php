<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subscription_id',
        'tenant_id',
        'payment_method',
        'amount',
        'currency',
        'payment_status',
        'transaction_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'qr_code',
        'upi_id',
        'upi_transaction_id',
        'screenshot_path',
        'notes',
        'paid_at',
        'failed_at',
        'refund_amount',
        'refund_reason',
        'refunded_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('payment_status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('payment_status', 'failed');
    }

    public function scopeRefunded($query)
    {
        return $query->where('payment_status', 'refunded');
    }

    public function scopeRazorpay($query)
    {
        return $query->where('payment_method', 'razorpay');
    }

    public function scopeUpiQr($query)
    {
        return $query->where('payment_method', 'upi_qr');
    }

    public function scopeManual($query)
    {
        return $query->where('payment_method', 'manual');
    }

    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->payment_status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->payment_status === 'refunded';
    }

    public function markAsCompleted(string $transactionId = null): void
    {
        $this->update([
            'payment_status' => 'completed',
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'paid_at' => now(),
        ]);

        // Update subscription payment status
        $this->load('subscription');
        if ($this->subscription) {
            $this->subscription->update([
                'payment_status' => 'paid',
                'payment_method' => $this->payment_method,
                'payment_id' => $this->transaction_id,
                'paid_at' => now(),
            ]);

            // Activate subscription if it was pending or trial
            if ($this->subscription->status === 'trial' || $this->subscription->status === 'pending') {
                $this->subscription->activate();
            }
        }
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'payment_status' => 'failed',
            'notes' => $reason ?? $this->notes,
            'failed_at' => now(),
        ]);

        if ($this->subscription) {
            $this->subscription->update([
                'payment_status' => 'failed',
            ]);
        }
    }

    public function markAsProcessing(): void
    {
        $this->update([
            'payment_status' => 'processing',
        ]);
    }

    public function refund(float $amount, string $reason): void
    {
        $this->update([
            'payment_status' => 'refunded',
            'refund_amount' => $amount,
            'refund_reason' => $reason,
            'refunded_at' => now(),
        ]);
    }

    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->currency;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'razorpay' => 'Razorpay',
            'upi_qr' => 'UPI QR Code',
            'manual' => 'Manual',
            default => ucfirst($this->payment_method),
        };
    }
}
