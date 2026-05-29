<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\PaymentRequest;
use App\Models\CourseSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $payments = PaymentRequest::where('student_id', $student->id)
            ->with(['course', 'book', 'enrollment'])
            ->latest()
            ->get();

        return view('student.payments.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $student   = Auth::user();
        $courseId  = $request->query('course_id');
        $payType   = $request->query('type', 'enrollment');

        $enrollments = Enrollment::where('student_id', $student->id)
            ->with('course')
            ->whereIn('enrollment_status', ['active', 'approved'])
            ->get();

        // Generate past months data for each enrollment
        $enrollmentsWithMonths = $enrollments->map(function ($enrollment) {
            $enrollmentStart = $enrollment->enrolled_at ?? $enrollment->created_at;
            $startDate = Carbon::parse($enrollmentStart)->startOfMonth();
            $endDate = Carbon::now()->startOfMonth();
            
            $months = [];
            $current = $startDate->copy();
            
            // Get already paid months for this enrollment
            $paidSubscriptions = CourseSubscription::where('enrollment_id', $enrollment->id)
                ->where('payment_status', 'paid')
                ->get()
                ->map(function ($sub) {
                    return $sub->access_start->format('Y-m');
                })
                ->toArray();
            
            while ($current <= $endDate) {
                $monthKey = $current->format('Y-m');
                $months[] = [
                    'year' => $current->year,
                    'month' => $current->month,
                    'month_name' => $current->format('F Y'),
                    'is_paid' => in_array($monthKey, $paidSubscriptions),
                    'month_key' => $monthKey,
                ];
                $current->addMonth();
            }
            
            $enrollment->past_months = $months;
            return $enrollment;
        });

        $allCourses = Course::where('tenant_id', $student->tenant_id)
            ->where('status', 'active')
            ->get();

        $selectedCourse = $courseId ? Course::find($courseId) : null;

        return view('student.payments.create', compact(
            'enrollmentsWithMonths', 'allCourses', 'selectedCourse', 'payType'
        ));
    }

    public function store(Request $request)
    {
        $student = Auth::user();

        $request->validate([
            'course_id'        => 'required|exists:courses,id',
            'payment_type'     => 'required|in:enrollment,monthly,past_month',
            'amount'           => 'required|numeric|min:1',
            'reference_number' => 'nullable|string|max:100',
            'screenshot'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'note'             => 'nullable|string|max:500',
            'month_number'     => 'nullable|integer|min:1|max:12',
            'year_number'      => 'nullable|integer|min:2020|max:2099',
            'past_months'      => 'nullable|array', // Array of selected past months ["2026-01", "2026-02"]
            'past_months.*'    => 'string', // Each item is YYYY-MM format
        ]);

        $enrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $request->course_id)
            ->first();

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('payment_screenshots', 'public');
        }

        // Handle past_months data - store as JSON in note field or create separate logic
        $note = $request->note;
        if ($request->payment_type === 'past_month' && !empty($request->past_months)) {
            $selectedMonths = implode(', ', $request->past_months);
            $note = ($note ? $note . "\n" : '') . "Selected months: " . $selectedMonths;
            
            // Store the first month in month_number/year_number for backward compatibility
            $firstMonth = $request->past_months[0] ?? null;
            if ($firstMonth) {
                [$year, $month] = explode('-', $firstMonth);
                $request->merge([
                    'month_number' => (int) $month,
                    'year_number' => (int) $year,
                ]);
            }
        }

        PaymentRequest::create([
            'tenant_id'        => $student->tenant_id,
            'student_id'       => $student->id,
            'course_id'        => $request->course_id,
            'enrollment_id'    => $enrollment?->id,
            'payment_type'     => $request->payment_type,
            'amount'           => $request->amount,
            'reference_number' => $request->reference_number,
            'screenshot'       => $screenshotPath,
            'note'             => $note,
            'month_number'     => $request->month_number,
            'year_number'      => $request->year_number,
            'status'           => 'pending',
            'metadata'         => $request->payment_type === 'past_month' && !empty($request->past_months) 
                ? json_encode(['past_months' => $request->past_months]) 
                : null,
        ]);

        return redirect()->route('student.payments.index')
            ->with('success', 'Payment request submitted successfully. Admin will verify and approve it shortly.');
    }
}
