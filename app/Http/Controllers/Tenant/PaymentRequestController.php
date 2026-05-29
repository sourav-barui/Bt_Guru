<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Book;
use App\Models\BookOrder;
use App\Models\User;
use App\Models\MonthlyFee;
use App\Models\CourseSubscription;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentRequestController extends Controller
{
    public function index(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $query  = PaymentRequest::where('tenant_id', $tenant->id)
            ->with(['student', 'course', 'book', 'enrollment']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        $payments = $query->latest()->paginate(20);
        $courses  = Course::where('tenant_id', $tenant->id)->get();

        $stats = [
            'pending'  => PaymentRequest::where('tenant_id', $tenant->id)->where('status', 'pending')->count(),
            'approved' => PaymentRequest::where('tenant_id', $tenant->id)->where('status', 'approved')->count(),
            'rejected' => PaymentRequest::where('tenant_id', $tenant->id)->where('status', 'rejected')->count(),
            'total_collected' => PaymentRequest::where('tenant_id', $tenant->id)->where('status', 'approved')->sum('amount'),
        ];

        return view('tenant.payments.index', compact('payments', 'courses', 'stats'));
    }

    public function approve(Request $request, PaymentRequest $payment)
    {
        $request->validate(['admin_remark' => 'nullable|string|max:500']);

        $payment->update([
            'status'       => 'approved',
            'admin_remark' => $request->admin_remark,
            'reviewed_by'  => Auth::id(),
            'reviewed_at'  => now(),
        ]);

        // Apply to enrollment if exists
        if ($payment->enrollment_id) {
            $enrollment = Enrollment::find($payment->enrollment_id);
            if ($enrollment) {
                $enrollment->addPayment((float) $payment->amount);
                // Activate enrollment if pending or rejected
                if (in_array($enrollment->enrollment_status, ['pending', 'rejected'])) {
                    $enrollment->update([
                        'enrollment_status' => 'active',
                        'enrolled_at'       => now(),
                        'approved_at'       => now(),
                        'approved_by'       => Auth::id(),
                    ]);
                }

                // Handle monthly fee payments
                if ($payment->payment_type === 'monthly' || $payment->payment_type === 'past_month') {
                    // Use current month/year if not set
                    $monthNum = $payment->month_number ?? now()->month;
                    $yearNum = $payment->year_number ?? now()->year;
                    
                    // Find or create monthly fee record
                    $monthlyFee = MonthlyFee::firstOrCreate(
                        [
                            'enrollment_id' => $enrollment->id,
                            'month' => $monthNum,
                            'year' => $yearNum,
                        ],
                        [
                            'tenant_id' => $payment->tenant_id,
                            'student_id' => $payment->student_id,
                            'amount' => $payment->amount,
                            'status' => 'pending',
                        ]
                    );

                    // Mark as paid
                    $monthlyFee->markAsPaid('online', $payment->reference_number);

                    // Create 30-day access window subscription starting from now
                    $accessStart = Carbon::now()->startOfSecond();
                    $accessEnd = Carbon::now()->addDays(30)->startOfSecond();
                    
                    \Log::info('Creating subscription (existing enrollment)', [
                        'accessStart' => $accessStart->toDateTimeString(),
                        'accessEnd' => $accessEnd->toDateTimeString(),
                    ]);
                    
                    CourseSubscription::create([
                        'tenant_id'       => $payment->tenant_id,
                        'enrollment_id'   => $enrollment->id,
                        'student_id'      => $payment->student_id,
                        'course_id'       => $payment->course_id,
                        'access_start'    => $accessStart,
                        'access_end'      => $accessEnd,
                        'type'            => 'monthly',
                        'fee_paid'        => $payment->amount,
                        'payment_status'  => 'paid',
                        'remarks'         => "Monthly fee for {$monthNum}/{$yearNum}",
                        'created_by'      => Auth::id(),
                    ]);
                }
            }
        } elseif ($payment->payment_type === 'enrollment') {
            // Create enrollment if doesn't exist
            $course = Course::find($payment->course_id);
            if ($course) {
                $enrollment = Enrollment::create([
                    'tenant_id'         => $payment->tenant_id,
                    'student_id'        => $payment->student_id,
                    'course_id'         => $payment->course_id,
                    'payment_status'    => $payment->amount >= $course->fees ? 'completed' : 'partial',
                    'enrollment_status' => 'active',
                    'fees_paid'         => $payment->amount,
                    'fees_total'        => $course->fees,
                    'enrolled_at'       => now(),
                    'approved_at'       => now(),
                    'approved_by'       => Auth::id(),
                ]);
                $payment->update(['enrollment_id' => $enrollment->id]);
            }
        } elseif ($payment->payment_type === 'monthly' || $payment->payment_type === 'past_month') {
            // Monthly fee payment - check for existing enrollment first
            // Use current month/year if not set
            $monthNumber = $payment->month_number ?? now()->month;
            $yearNumber = $payment->year_number ?? now()->year;
            
            \Log::info('Processing monthly payment approval', [
                'payment_id' => $payment->id,
                'type' => $payment->payment_type,
                'month' => $monthNumber,
                'year' => $yearNumber,
                'has_enrollment_id' => $payment->enrollment_id ? 'yes' : 'no',
            ]);
            $course = Course::find($payment->course_id);
            if ($course) {
                // Find existing enrollment or create new one
                $enrollment = Enrollment::firstOrCreate(
                    [
                        'tenant_id'  => $payment->tenant_id,
                        'student_id' => $payment->student_id,
                        'course_id'  => $payment->course_id,
                    ],
                    [
                        'payment_status'    => 'partial',
                        'enrollment_status' => 'active',
                        'fees_paid'         => $payment->amount,
                        'fees_total'        => $course->fees,
                        'enrolled_at'       => now(),
                        'approved_at'       => now(),
                        'approved_by'       => Auth::id(),
                    ]
                );

                // If enrollment was newly created, fees_paid is already set
                // If enrollment existed, add the payment amount
                if (!$enrollment->wasRecentlyCreated) {
                    $enrollment->addPayment((float) $payment->amount);
                    // Activate enrollment if pending or rejected
                    if (in_array($enrollment->enrollment_status, ['pending', 'rejected'])) {
                        $enrollment->update([
                            'enrollment_status' => 'active',
                            'enrolled_at'       => now(),
                            'approved_at'       => now(),
                            'approved_by'       => Auth::id(),
                        ]);
                    }
                }

                $payment->update(['enrollment_id' => $enrollment->id]);
                \Log::info('Monthly payment enrollment linked', [
                    'payment_id' => $payment->id,
                    'enrollment_id' => $enrollment->id,
                    'enrollment_status' => $enrollment->enrollment_status,
                ]);

                // Create monthly fee record and mark as paid
                $monthlyFee = MonthlyFee::firstOrCreate(
                    [
                        'enrollment_id' => $enrollment->id,
                        'month' => $monthNumber,
                        'year' => $yearNumber,
                    ],
                    [
                        'tenant_id' => $payment->tenant_id,
                        'student_id' => $payment->student_id,
                        'amount' => $payment->amount,
                        'status' => 'pending',
                    ]
                );
                $monthlyFee->markAsPaid('online', $payment->reference_number);

                // Create 30-day access window subscription starting from now
                $accessStart = Carbon::now()->startOfSecond();
                $accessEnd = Carbon::now()->addDays(30)->startOfSecond();
                
                \Log::info('Creating subscription', [
                    'accessStart' => $accessStart->toDateTimeString(),
                    'accessEnd' => $accessEnd->toDateTimeString(),
                    'timestamp' => now()->toDateTimeString(),
                ]);
                
                CourseSubscription::create([
                    'tenant_id'       => $payment->tenant_id,
                    'enrollment_id'   => $enrollment->id,
                    'student_id'      => $payment->student_id,
                    'course_id'       => $payment->course_id,
                    'access_start'    => $accessStart,
                    'access_end'      => $accessEnd,
                    'type'            => 'monthly',
                    'fee_paid'        => $payment->amount,
                    'payment_status'  => 'paid',
                    'remarks'         => "Monthly fee for {$monthNumber}/{$yearNumber}",
                    'created_by'      => Auth::id(),
                ]);
            }
        }

        // Handle book purchase
        if ($payment->payment_type === 'book_purchase' && $payment->book_id) {
            $book = Book::find($payment->book_id);
            if ($book) {
                $metadata = $payment->metadata ?? [];
                $orderType = $metadata['order_type'] ?? 'pdf';
                $pdfPrice = $metadata['pdf_price'] ?? 0;
                $physicalPrice = $metadata['physical_price'] ?? 0;

                BookOrder::create([
                    'tenant_id'       => $payment->tenant_id,
                    'book_id'         => $book->id,
                    'student_id'      => $payment->student_id,
                    'order_type'      => $orderType,
                    'pdf_price'       => $pdfPrice,
                    'physical_price'  => $physicalPrice,
                    'total_amount'    => $payment->amount,
                    'payment_status'  => 'completed',
                    'payment_method'  => 'manual',
                    'transaction_id'  => $payment->reference_number,
                    'paid_at'         => now(),
                    'delivery_status' => in_array($orderType, ['physical', 'both']) ? 'pending' : null,
                    'delivery_address'=> $metadata['delivery_address'] ?? null,
                    'delivery_phone'  => $metadata['delivery_phone'] ?? null,
                    'notes'           => $payment->note,
                ]);

                // Decrease stock for physical books
                if (in_array($orderType, ['physical', 'both'])) {
                    $book->decrement('stock_quantity');
                }
            }
        }

        // Send payment verification notification to student
        try {
            $payment->load(['student', 'course', 'book']);
            if ($payment->student) {
                $itemTitle = $payment->book?->title ?? $payment->course?->title ?? 'your purchase';
                if ($payment->course) {
                    (new NotificationService())->paymentVerified(
                        Auth::user()->tenant,
                        $payment->student,
                        $itemTitle
                    );
                }
            }
        } catch (\Throwable $e) {
            \Log::warning('Payment verification notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Payment approved successfully.');
    }

    public function reject(Request $request, PaymentRequest $payment)
    {
        $request->validate(['admin_remark' => 'required|string|max:500']);

        $payment->update([
            'status'       => 'rejected',
            'admin_remark' => $request->admin_remark,
            'reviewed_by'  => Auth::id(),
            'reviewed_at'  => now(),
        ]);

        return back()->with('success', 'Payment request rejected.');
    }

    public function show(PaymentRequest $payment)
    {
        $payment->load(['student', 'course', 'enrollment', 'reviewer']);
        return view('tenant.payments.show', compact('payment'));
    }

    public function enroll(Request $request, PaymentRequest $payment)
    {
        // Only allow enrolling from approved payments without existing enrollment
        if ($payment->status !== 'approved') {
            return back()->with('error', 'Payment must be approved before enrolling.');
        }

        if ($payment->enrollment_id) {
            return back()->with('error', 'Student is already enrolled for this payment.');
        }

        // Check if student already has an enrollment for this course
        $existingEnrollment = Enrollment::where('student_id', $payment->student_id)
            ->where('course_id', $payment->course_id)
            ->where('tenant_id', $payment->tenant_id)
            ->first();

        if ($existingEnrollment) {
            // Link payment to existing enrollment and add payment amount
            $payment->update(['enrollment_id' => $existingEnrollment->id]);
            $existingEnrollment->addPayment((float) $payment->amount);
            return back()->with('success', 'Payment linked to existing enrollment.');
        }

        // Create new enrollment
        $course = Course::find($payment->course_id);
        if (!$course) {
            return back()->with('error', 'Course not found.');
        }

        $enrollment = Enrollment::create([
            'tenant_id'         => $payment->tenant_id,
            'student_id'        => $payment->student_id,
            'course_id'         => $payment->course_id,
            'payment_status'    => $payment->amount >= $course->fees ? 'completed' : 'partial',
            'enrollment_status' => 'active',
            'fees_paid'         => $payment->amount,
            'fees_total'        => $course->fees,
            'enrolled_at'       => now(),
            'approved_at'       => now(),
            'approved_by'       => Auth::id(),
        ]);

        // Link payment to new enrollment
        $payment->update(['enrollment_id' => $enrollment->id]);

        // Send enrollment notification
        try {
            $payment->load(['student', 'course']);
            if ($payment->student && $payment->course) {
                (new NotificationService())->enrollmentApproved(
                    Auth::user()->tenant,
                    $payment->student,
                    $payment->course
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Enrollment notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Student enrolled successfully.');
    }

    public function rewind(Request $request, PaymentRequest $payment)
    {
        // Only approved payments can be rewound
        if ($payment->status !== 'approved') {
            return back()->with('error', 'Only approved payments can be reset to pending.');
        }

        // Revert the payment status back to pending
        $payment->update([
            'status'       => 'pending',
            'admin_remark' => null,
            'reviewed_by'  => null,
            'reviewed_at'  => null,
        ]);

        // If there was an enrollment created from this payment, we should handle it
        // Option 1: Delete the enrollment if no other payments linked
        // Option 2: Keep it but mark as pending
        // For now, we'll just unlink the payment from enrollment
        if ($payment->enrollment_id) {
            $enrollment = Enrollment::find($payment->enrollment_id);
            if ($enrollment) {
                // Subtract the payment amount from enrollment
                $enrollment->fees_paid = max(0, $enrollment->fees_paid - $payment->amount);
                // Update payment status based on remaining fees
                if ($enrollment->fees_paid >= $enrollment->fees_total) {
                    $enrollment->payment_status = 'completed';
                } elseif ($enrollment->fees_paid > 0) {
                    $enrollment->payment_status = 'partial';
                } else {
                    $enrollment->payment_status = 'pending';
                }
                $enrollment->save();
            }
            // Unlink payment from enrollment
            $payment->update(['enrollment_id' => null]);
            
            // Check if this was the only payment for this enrollment
            $otherPayments = \App\Models\PaymentRequest::where('enrollment_id', $enrollment->id)
                ->where('id', '!=', $payment->id)
                ->where('status', 'approved')
                ->count();
            
            // If no other approved payments, suspend the enrollment
            if ($otherPayments === 0) {
                $enrollment->update(['enrollment_status' => 'pending']);
            }
        }

        return back()->with('success', 'Payment reset to pending status.');
    }
}
