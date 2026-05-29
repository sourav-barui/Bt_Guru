<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'course_id',
        'book_id',
        'enrollment_id',
        'payment_type',
        'amount',
        'reference_number',
        'screenshot',
        'note',
        'status',
        'admin_remark',
        'reviewed_by',
        'reviewed_at',
        'month_number',
        'year_number',
        'metadata',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'reviewed_at' => 'datetime',
        'metadata'    => 'array',
    ];

    public function tenant()      { return $this->belongsTo(Tenant::class); }
    public function student()     { return $this->belongsTo(User::class, 'student_id'); }
    public function course()      { return $this->belongsTo(Course::class); }
    public function book()        { return $this->belongsTo(Book::class); }
    public function enrollment()  { return $this->belongsTo(Enrollment::class); }
    public function reviewer()    { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function isPending()  { return $this->status === 'pending'; }
    public function isApproved() { return $this->status === 'approved'; }
    public function isRejected() { return $this->status === 'rejected'; }

    public function getPaymentTypeLabelAttribute(): string
    {
        return match($this->payment_type) {
            'enrollment'    => 'Course Enrollment',
            'monthly'       => 'Monthly Fee',
            'past_month'    => 'Past Month Access',
            'book_purchase' => 'Book Purchase',
            default         => ucfirst($this->payment_type),
        };
    }

    public function getMonthLabelAttribute(): ?string
    {
        if (!$this->month_number || !$this->year_number) return null;
        return \Carbon\Carbon::createFromDate($this->year_number, $this->month_number, 1)->format('F Y');
    }
}
