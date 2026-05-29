<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\Course;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EnrollmentController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $tenantId = Auth::user()->tenant_id;
        $query = Enrollment::where('tenant_id', $tenantId)
            ->with(['student', 'course', 'paymentRequests']);

        if ($request->status) {
            $query->where('enrollment_status', $request->status);
        }
        if ($request->payment) {
            $query->where('payment_status', $request->payment);
        }
        if ($request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', fn($s) => $s->where('name', 'like', $search)->orWhere('email', 'like', $search))
                  ->orWhereHas('course', fn($c) => $c->where('title', 'like', $search));
            });
        }

        $enrollments = $query->latest()->paginate(20);

        $statusCounts = [
            'pending'  => Enrollment::where('tenant_id', $tenantId)->where('enrollment_status', 'pending')->count(),
            'approved' => Enrollment::where('tenant_id', $tenantId)->where('enrollment_status', 'approved')->count(),
            'active'   => Enrollment::where('tenant_id', $tenantId)->where('enrollment_status', 'active')->count(),
            'rejected' => Enrollment::where('tenant_id', $tenantId)->where('enrollment_status', 'rejected')->count(),
        ];

        return view('tenant.enrollments.index', compact('enrollments', 'statusCounts'));
    }

    public function create()
    {
        $students = User::where('tenant_id', Auth::user()->tenant_id)
            ->whereHas('roles', function ($q) { $q->where('name', 'student'); })
            ->get();
        
        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->where('status', 'active')
            ->get();

        return view('tenant.enrollments.create', compact('students', 'courses'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'payment_status' => 'required|in:pending,partial,completed',
            'enrollment_status' => 'required|in:pending,approved,active',
            'fees_paid' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $course = Course::find($request->course_id);
        $feesPaid = $request->fees_paid ?? 0;
        
        $enrollment = Enrollment::create([
            'tenant_id' => Auth::user()->tenant_id,
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'payment_status' => $request->payment_status,
            'enrollment_status' => $request->enrollment_status,
            'fees_paid' => $feesPaid,
            'fees_total' => $course->fees,
            'enrolled_at' => $request->enrollment_status === 'active' ? now() : null,
            'approved_at' => in_array($request->enrollment_status, ['approved', 'active']) ? now() : null,
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('tenant.enrollments.index')
            ->with('success', 'Enrollment created successfully.');
    }

    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'course', 'course.teachers']);
        return view('tenant.enrollments.show', compact('enrollment'));
    }

    public function edit(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'course', 'paymentRequests']);

        $students = User::where('tenant_id', Auth::user()->tenant_id)
            ->whereHas('roles', function ($q) { $q->where('name', 'student'); })
            ->get();
        
        $courses = Course::where('tenant_id', Auth::user()->tenant_id)
            ->where('status', 'active')
            ->get();

        return view('tenant.enrollments.edit', compact('enrollment', 'students', 'courses'));
    }

    public function update(Request $request, Enrollment $enrollment)
    {
        $validator = Validator::make($request->all(), [
            'payment_status' => 'required|in:pending,partial,completed,refunded',
            'enrollment_status' => 'required|in:pending,approved,active,rejected,dropped,completed',
            'fees_paid' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $data = [
            'payment_status' => $request->payment_status,
            'enrollment_status' => $request->enrollment_status,
            'fees_paid' => $request->fees_paid ?? $enrollment->fees_paid,
            'remarks' => $request->remarks,
        ];

        if ($request->enrollment_status === 'active' && !$enrollment->enrolled_at) {
            $data['enrolled_at'] = now();
        }

        if (in_array($request->enrollment_status, ['approved', 'active']) && !$enrollment->approved_at) {
            $data['approved_at'] = now();
            $data['approved_by'] = Auth::id();
        }

        $enrollment->update($data);

        return back()->with('success', 'Enrollment updated successfully.');
    }

    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();
        return redirect()->route('tenant.enrollments.index')
            ->with('success', 'Enrollment deleted successfully.');
    }

    public function approve(Enrollment $enrollment)
    {
        $enrollment->markAsApproved(Auth::id());

        // Send notification to student
        try {
            $enrollment->load(['student', 'course']);
            if ($enrollment->student && $enrollment->course) {
                (new NotificationService())->enrollmentApproved(
                    Auth::user()->tenant,
                    $enrollment->student,
                    $enrollment->course
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Enrollment approval notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Enrollment approved successfully.');
    }

    public function activate(Enrollment $enrollment)
    {
        $enrollment->markAsActive();

        // Send notification to student if not already sent during approval
        try {
            $enrollment->load(['student', 'course']);
            if ($enrollment->student && $enrollment->course) {
                (new NotificationService())->send(
                    Auth::user()->tenant,
                    $enrollment->student,
                    type: 'course',
                    title: "Enrollment Activated: {$enrollment->course->title}",
                    body: "Your enrollment in {$enrollment->course->title} is now active. You can start accessing course content!",
                    icon: 'course',
                    url: '/student/courses',
                    sendEmail: true
                );
            }
        } catch (\Throwable $e) {
            \Log::warning('Enrollment activation notification failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Enrollment activated successfully.');
    }

    public function addPayment(Request $request, Enrollment $enrollment)
    {
        $validator = Validator::make($request->all(), [
            'amount'           => 'required|numeric|min:1',
            'reference_number' => 'nullable|string|max:100',
            'month_number'     => 'nullable|integer|min:1|max:12',
            'year_number'      => 'nullable|integer|min:2020|max:2099',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $isMonthly = $enrollment->course->fees_type === 'monthly';

        // Create a PaymentRequest record (auto-approved since admin is adding it)
        \App\Models\PaymentRequest::create([
            'tenant_id'        => $enrollment->tenant_id,
            'student_id'       => $enrollment->student_id,
            'course_id'        => $enrollment->course_id,
            'enrollment_id'    => $enrollment->id,
            'payment_type'     => $isMonthly ? 'monthly' : 'enrollment',
            'amount'           => $request->amount,
            'reference_number' => $request->reference_number,
            'month_number'     => $request->month_number,
            'year_number'      => $request->year_number,
            'status'           => 'approved',
            'admin_remark'     => 'Added manually by admin',
            'reviewed_by'      => Auth::id(),
            'reviewed_at'      => now(),
        ]);

        $enrollment->addPayment((float) $request->amount);

        return back()->with('success', 'Payment of ₹'.number_format($request->amount).' recorded successfully.');
    }
}
