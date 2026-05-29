<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'book_id',
        'student_id',
        'order_type',
        'pdf_price',
        'physical_price',
        'total_amount',
        'payment_status',
        'payment_method',
        'transaction_id',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'delivery_status',
        'delivery_address',
        'delivery_phone',
        'tracking_number',
        'delivered_at',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'pdf_price' => 'decimal:2',
        'physical_price' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'delivered_at' => 'datetime',
        'paid_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'payment_status' => 'pending',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('payment_status', 'completed');
    }

    public function scopePdfOrders($query)
    {
        return $query->whereIn('order_type', ['pdf', 'both']);
    }

    public function scopePhysicalOrders($query)
    {
        return $query->whereIn('order_type', ['physical', 'both']);
    }

    public function isCompleted(): bool
    {
        return $this->payment_status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->payment_status === 'pending';
    }

    public function isPdfOrder(): bool
    {
        return in_array($this->order_type, ['pdf', 'both']);
    }

    public function isPhysicalOrder(): bool
    {
        return in_array($this->order_type, ['physical', 'both']);
    }

    public function canDownload(): bool
    {
        return $this->isCompleted() && $this->isPdfOrder() && $this->book && $this->book->pdf_file;
    }

    public function markAsCompleted(string $transactionId = null): void
    {
        $this->update([
            'payment_status' => 'completed',
            'transaction_id' => $transactionId ?? $this->transaction_id,
            'paid_at' => now(),
        ]);

        // If physical book, set delivery status to pending
        if ($this->isPhysicalOrder()) {
            $this->update(['delivery_status' => 'pending']);

            // Decrease stock quantity
            if ($this->book) {
                $this->book->decrement('stock_quantity');
            }
        }
    }

    public function markAsFailed(string $reason = null): void
    {
        $this->update([
            'payment_status' => 'failed',
            'notes' => $reason ?? $this->notes,
        ]);
    }

    public function updateDeliveryStatus(string $status, array $data = []): void
    {
        $updateData = ['delivery_status' => $status];

        if (isset($data['tracking_number'])) {
            $updateData['tracking_number'] = $data['tracking_number'];
        }

        if ($status === 'delivered') {
            $updateData['delivered_at'] = now();
        }

        $this->update($updateData);
    }

    public function getPaymentStatusBadgeClassAttribute(): string
    {
        return match($this->payment_status) {
            'completed' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary',
        };
    }

    public function getDeliveryStatusBadgeClassAttribute(): string
    {
        return match($this->delivery_status) {
            'delivered' => 'success',
            'shipped' => 'info',
            'processing' => 'warning',
            'pending' => 'secondary',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }

    public function getOrderTypeLabelAttribute(): string
    {
        return match($this->order_type) {
            'pdf' => 'PDF Only',
            'physical' => 'Physical Only',
            'both' => 'PDF & Physical',
            default => ucfirst($this->order_type),
        };
    }
}
