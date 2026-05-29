<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyFee extends Model
{
    protected $fillable = [
        'tenant_id',
        'enrollment_id',
        'student_id',
        'year',
        'month',
        'amount',
        'status',
        'paid_at',
        'payment_method',
        'transaction_id',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function getMonthNameAttribute()
    {
        return date('F', mktime(0, 0, 0, $this->month, 1));
    }

    public function getIsOverdueAttribute()
    {
        if ($this->status === 'paid') return false;
        $dueDate = now()->setDate($this->year, $this->month, 5); // Due by 5th of month
        return now()->greaterThan($dueDate);
    }

    public function markAsPaid($paymentMethod, $transactionId = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
        ]);

        // Check if all monthly fees are paid, update enrollment
        $this->updateEnrollmentStatus();
    }

    private function updateEnrollmentStatus()
    {
        $enrollment = $this->enrollment;
        $pendingFees = $enrollment->monthlyFees()->whereIn('status', ['pending', 'overdue'])->count();
        
        if ($pendingFees === 0) {
            $enrollment->update([
                'payment_status' => 'completed',
                'fees_paid' => $enrollment->monthlyFees()->where('status', 'paid')->sum('amount'),
            ]);
        } else {
            $enrollment->update([
                'payment_status' => 'partial',
                'fees_paid' => $enrollment->monthlyFees()->where('status', 'paid')->sum('amount'),
            ]);
        }
    }
}
