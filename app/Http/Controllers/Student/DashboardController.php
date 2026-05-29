<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Chapter;
use App\Models\Lesson;
use App\Models\CourseSubscription;
use App\Models\StudentNotification;
use App\Models\MonthlyFee;
use App\Models\Notice;
use App\Models\CurriculumNote;
use App\Services\MonthlyFeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        
        // Ensure student has tenant_id
        if (!$student->tenant_id) {
            abort(403, 'Invalid user configuration');
        }

        $activeEnrollments = $student->enrollments()
            ->where('enrollment_status', 'active')
            ->with(['course', 'course.teachers'])
            ->get();

        $stats = [
            'enrolled_courses' => $activeEnrollments->count(),
            'pending_payments' => $student->enrollments()
                ->where('payment_status', 'pending')
                ->count(),
            'completed_courses' => $student->enrollments()
                ->where('enrollment_status', 'completed')
                ->count(),
        ];

        $notices = \App\Models\Notice::where('tenant_id', $student->tenant_id)
            ->active()
            ->forStudents()
            ->latest()
            ->take(5)
            ->get();

        $unreadNotificationsCount = StudentNotification::where('user_id', $student->id)
            ->where('is_read', false)
            ->count();

        return view('student.dashboard', compact('student', 'activeEnrollments', 'stats', 'notices', 'unreadNotificationsCount'));
    }

    public function myCourses()
    {
        $student = Auth::user();
        $enrollments = $student->enrollments()
            ->whereIn('enrollment_status', ['active', 'pending', 'completed'])
            ->with(['course', 'course.teachers'])
            ->latest()
            ->paginate(10);

        return view('student.courses', compact('enrollments'));
    }

    public function feeStatus()
    {
        $student = Auth::user();
        $enrollments = $student->enrollments()
            ->with(['course'])
            ->where('enrollment_status', '!=', 'rejected')
            ->get();

        $totalFees = $enrollments->sum('fees_total');
        $totalPaid = $enrollments->sum('fees_paid');
        $totalBalance = $totalFees - $totalPaid;

        return view('student.fees', compact('enrollments', 'totalFees', 'totalPaid', 'totalBalance'));
    }

    public function allCourses()
    {
        $student = Auth::user();
        
        // Get IDs of courses the student is already enrolled in
        $enrolledCourseIds = $student->enrollments()
            ->whereIn('enrollment_status', ['active', 'pending', 'completed'])
            ->pluck('course_id')
            ->toArray();
        
        // Get all active courses that the student is NOT enrolled in
        $availableCourses = Course::where('tenant_id', $student->tenant_id)
            ->where('status', 'active')
            ->whereNotIn('id', $enrolledCourseIds)
            ->with('teachers')
            ->latest()
            ->paginate(9);
        
        return view('student.courses_all', compact('availableCourses'));
    }

    /**
     * Get all paid access date ranges for a student on a monthly course.
     * Returns a flat collection of [access_start, access_end] Carbon pairs.
     */
    private function getAccessWindows($studentId, $courseId)
    {
        return CourseSubscription::where('student_id', $studentId)
            ->where('course_id', $courseId)
            ->where('payment_status', 'paid')
            ->get(['access_start', 'access_end']);
    }

    /**
     * For a monthly course, determine if a given date is within any paid window.
     * Returns true (accessible) or false (locked).
     * For one_time courses always returns true.
     */
    private function isDateAccessible($date, $accessWindows)
    {
        if ($date === null) return true; // no date set = always accessible
        $d = Carbon::parse($date);
        foreach ($accessWindows as $w) {
            if ($d->between($w->access_start, $w->access_end)) {
                return true;
            }
        }
        return false;
    }

    public function accessCourse(Course $course)
    {
        $student = Auth::user();

        $enrollment = $student->enrollments()
            ->where('course_id', $course->id)
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.courses.all')
                ->with('error', 'You need to purchase this course to access its content.');
        }

        $course->load([
            'curricula' => function($query) {
                $query->orderBy('order')->with([
                    'subjects' => function($sq) {
                        $sq->orderBy('order')->with([
                            'contents.user',
                            'notes.user',
                            'chapters' => function($cq) {
                                $cq->orderBy('order')->with([
                                    'contents.user',
                                    'notes.user',
                                    'lessons' => function($lq) {
                                        $lq->orderBy('order')->with(['contents.user', 'notes.user']);
                                    }
                                ]);
                            }
                        ]);
                    }
                ]);
            },
            'teachers',
            'liveClasses' => function($q) {
                $q->whereIn('status', ['scheduled', 'live'])
                  ->orderBy('scheduled_at');
            },
            'exams' => function($q) {
                $q->where('status', 'published')
                  ->where(function($q) {
                      $q->whereNull('start_time')
                        ->orWhere('start_time', '<=', now());
                  })
                  ->with(['sections']);
            },
        ]);

        $accessWindows = $course->fees_type === 'monthly'
            ? $this->getAccessWindows($student->id, $course->id)
            : collect();

        return view('student.course_access', compact('course', 'enrollment', 'accessWindows'));
    }

    public function viewSubject(Course $course, Subject $subject)
    {
        $student = Auth::user();

        $enrollment = $student->enrollments()
            ->where('course_id', $course->id)
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.courses.all')
                ->with('error', 'You need to purchase this course to access its content.');
        }

        $subject->load([
            'chapters' => function($q) { $q->orderBy('order'); },
            'contents.user',
            'notes.user',
            'liveClasses' => function($q) { $q->orderBy('scheduled_at'); }
        ]);

        $course->load([
            'exams' => function($q) {
                $q->where('status', 'published')
                  ->where(function($q) {
                      $q->whereNull('start_time')
                        ->orWhere('start_time', '<=', now());
                  });
            }
        ]);

        $accessWindows = $course->fees_type === 'monthly'
            ? $this->getAccessWindows($student->id, $course->id)
            : collect();

        return view('student.subject_detail', compact('course', 'subject', 'enrollment', 'accessWindows'));
    }

    public function viewChapter(Course $course, Subject $subject, Chapter $chapter)
    {
        $student = Auth::user();

        $enrollment = $student->enrollments()
            ->where('course_id', $course->id)
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.courses.all')
                ->with('error', 'You need to purchase this course to access its content.');
        }

        $chapter->load([
            'lessons' => function($q) { $q->orderBy('order'); },
            'contents.user',
            'notes.user',
            'liveClasses' => function($q) { $q->orderBy('scheduled_at'); }
        ]);

        $course->load([
            'exams' => function($q) {
                $q->where('status', 'published')
                  ->where(function($q) {
                      $q->whereNull('start_time')
                        ->orWhere('start_time', '<=', now());
                  });
            }
        ]);

        $accessWindows = $course->fees_type === 'monthly'
            ? $this->getAccessWindows($student->id, $course->id)
            : collect();

        return view('student.chapter_detail', compact('course', 'subject', 'chapter', 'enrollment', 'accessWindows'));
    }

    public function viewLesson(Course $course, Subject $subject, Chapter $chapter, Lesson $lesson)
    {
        $student = Auth::user();

        $enrollment = $student->enrollments()
            ->where('course_id', $course->id)
            ->whereIn('enrollment_status', ['active', 'completed'])
            ->first();

        if (!$enrollment) {
            return redirect()->route('student.courses.all')
                ->with('error', 'You need to purchase this course to access its content.');
        }

        $lesson->load(['contents.user', 'notes.user', 'liveClasses']);

        $course->load([
            'exams' => function($q) {
                $q->where('status', 'published')
                  ->where(function($q) {
                      $q->whereNull('start_time')
                        ->orWhere('start_time', '<=', now());
                  });
            }
        ]);

        $accessWindows = $course->fees_type === 'monthly'
            ? $this->getAccessWindows($student->id, $course->id)
            : collect();

        return view('student.lesson_detail', compact('course', 'subject', 'chapter', 'lesson', 'enrollment', 'accessWindows'));
    }

    // ==================== MONTHLY FEES ====================

    public function monthlyFees()
    {
        $student = Auth::user();

        // Get active enrollments with their monthly fees
        $enrollments = $student->enrollments()
            ->where('enrollment_status', 'active')
            ->with(['course', 'pendingMonthlyFees'])
            ->get();

        // Calculate totals
        $totalPending = 0;
        $overdueCount = 0;

        foreach ($enrollments as $enrollment) {
            foreach ($enrollment->pendingMonthlyFees as $fee) {
                $totalPending += $fee->amount;
                if ($fee->status === 'overdue') {
                    $overdueCount++;
                }
            }
        }

        // Get paid fees for history
        $paidFees = MonthlyFee::where('student_id', $student->id)
            ->where('status', 'paid')
            ->with('enrollment.course')
            ->latest('paid_at')
            ->take(10)
            ->get();

        return view('student.monthly_fees', compact(
            'enrollments',
            'totalPending',
            'overdueCount',
            'paidFees'
        ));
    }

    public function payMonthlyFee(MonthlyFee $fee)
    {
        // Verify ownership
        if ($fee->student_id !== Auth::id()) {
            abort(403);
        }

        if ($fee->status === 'paid') {
            return redirect()->route('student.monthly-fees')
                ->with('error', 'This fee has already been paid.');
        }

        // Get tenant settings for UPI
        $tenant = Auth::user()->tenant;
        $settings = $tenant->settings ?? [];
        $upiId = $settings['upi_id'] ?? null;

        // Generate UPI payment link
        $amount = $fee->amount;
        $note = "Fee: {$fee->month_name} {$fee->year} - {$fee->enrollment->course->title}";

        if ($upiId) {
            $upiLink = "upi://pay?pa={$upiId}&pn={$tenant->coaching_name}&am={$amount}&cu=INR&tn=" . urlencode($note);
        }

        return view('student.pay_monthly_fee', compact('fee', 'upiLink', 'upiId', 'amount', 'note'));
    }

    public function payAllMonthlyFees()
    {
        $student = Auth::user();

        // Get all pending fees
        $pendingFees = MonthlyFee::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'overdue'])
            ->with('enrollment.course')
            ->get();

        if ($pendingFees->isEmpty()) {
            return redirect()->route('student.monthly-fees')
                ->with('success', 'No pending fees to pay.');
        }

        $totalAmount = $pendingFees->sum('amount');

        // Get tenant settings for UPI
        $tenant = $student->tenant;
        $settings = $tenant->settings ?? [];
        $upiId = $settings['upi_id'] ?? null;

        // Generate UPI link for total amount
        $note = "All Pending Fees - " . $pendingFees->count() . " months";
        $upiLink = null;
        if ($upiId) {
            $upiLink = "upi://pay?pa={$upiId}&pn={$tenant->coaching_name}&am={$totalAmount}&cu=INR&tn=" . urlencode($note);
        }

        return view('student.pay_all_fees', compact('pendingFees', 'totalAmount', 'upiId', 'upiLink', 'note'));
    }

    public function processAllMonthlyFees(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:upi,cash,other',
            'transaction_id' => 'required|string|max:255',
            'screenshot' => 'nullable|image|max:2048',
        ]);

        $student = Auth::user();

        // Get all pending fees
        $pendingFees = MonthlyFee::where('student_id', $student->id)
            ->whereIn('status', ['pending', 'overdue'])
            ->get();

        if ($pendingFees->isEmpty()) {
            return redirect()->route('student.monthly-fees')
                ->with('error', 'No pending fees to pay.');
        }

        $totalAmount = $pendingFees->sum('amount');

        // Handle screenshot upload
        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('payment_screenshots', 'public');
        }

        // Mark all fees as paid
        foreach ($pendingFees as $fee) {
            $fee->markAsPaid($request->payment_method, $request->transaction_id);

            // Create payment record
            \App\Models\Payment::create([
                'tenant_id' => $fee->tenant_id,
                'student_id' => $fee->student_id,
                'enrollment_id' => $fee->enrollment_id,
                'amount' => $fee->amount,
                'payment_method' => $request->payment_method,
                'transaction_id' => $request->transaction_id,
                'status' => 'completed',
                'notes' => "Monthly fee for {$fee->month_name} {$fee->year} (Bulk Payment)",
                'paid_at' => now(),
                'screenshot' => $screenshotPath,
            ]);
        }

        // Send notification
        try {
            (new \App\Services\NotificationService())->send(
                $student->tenant,
                $student,
                type: 'payment',
                title: "Payment Successful",
                body: "₹{$totalAmount} paid for {$pendingFees->count()} months",
                icon: 'payment',
                url: '/student/fees',
                sendEmail: true
            );
        } catch (\Throwable $e) {
            \Log::warning('Payment notification failed: ' . $e->getMessage());
        }

        return redirect()->route('student.monthly-fees')
            ->with('success', "Payment of ₹{$totalAmount} for {$pendingFees->count()} months recorded successfully!");
    }

    public function processMonthlyFeePayment(Request $request, MonthlyFee $fee)
    {
        // Verify ownership
        if ($fee->student_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'payment_method' => 'required|in:upi,cash,other',
            'transaction_id' => 'nullable|string|max:255',
            'screenshot' => 'nullable|image|max:2048',
        ]);

        // Handle screenshot upload
        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('payment_screenshots', 'public');
        }

        // Mark fee as paid
        $fee->markAsPaid($request->payment_method, $request->transaction_id);

        // Create payment record for tracking
        \App\Models\Payment::create([
            'tenant_id' => $fee->tenant_id,
            'student_id' => $fee->student_id,
            'enrollment_id' => $fee->enrollment_id,
            'amount' => $fee->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'status' => 'completed',
            'notes' => "Monthly fee for {$fee->month_name} {$fee->year}",
            'paid_at' => now(),
            'screenshot' => $screenshotPath,
        ]);

        // Send notification
        try {
            (new \App\Services\NotificationService())->send(
                $fee->tenant,
                $fee->student,
                type: 'payment',
                title: "Payment Successful",
                body: "₹{$fee->amount} paid for {$fee->month_name} {$fee->year}",
                icon: 'payment',
                url: '/student/fees',
                sendEmail: true
            );
        } catch (\Throwable $e) {
            \Log::warning('Payment notification failed: ' . $e->getMessage());
        }

        return redirect()->route('student.monthly-fees')
            ->with('success', "Payment of ₹{$fee->amount} for {$fee->month_name} {$fee->year} recorded successfully!");
    }

    /**
     * View a specific notice
     */
    public function viewNotice(\App\Models\Notice $notice)
    {
        $student = Auth::user();

        // Verify the notice belongs to the student's tenant
        if ($notice->tenant_id !== $student->tenant_id) {
            abort(403, 'Unauthorized access to this notice.');
        }

        // Check if notice is for students or all
        $canView = in_array($notice->audience, ['all', 'students']);

        // If notice is for a specific course, check enrollment
        if ($canView && $notice->course_id) {
            $isEnrolled = $student->enrollments()
                ->where('course_id', $notice->course_id)
                ->where('enrollment_status', 'active')
                ->exists();

            $canView = $isEnrolled;
        }

        if (!$canView) {
            abort(403, 'You do not have access to this notice.');
        }

        return view('student.notice_show', compact('notice'));
    }

    public function viewNote(CurriculumNote $note)
    {
        $student = Auth::user();

        // Verify the note belongs to the student's tenant (skip if tenant_id is null - legacy data)
        if ($note->tenant_id !== null && $note->tenant_id !== $student->tenant_id) {
            abort(403, 'Unauthorized access to this note. Tenant mismatch.');
        }

        // Get student's enrolled course IDs
        $enrolledCourseIds = $student->enrollments()
            ->where('enrollment_status', 'active')
            ->pluck('course_id')
            ->toArray();

        // Get course ID from note
        $courseId = null;
        if ($note->noteable_type === 'App\Models\Subject') {
            $subject = Subject::find($note->noteable_id);
            $courseId = $subject?->curriculum?->course_id;
        } elseif ($note->noteable_type === 'App\Models\Chapter') {
            $chapter = Chapter::with('subject.curriculum')->find($note->noteable_id);
            $courseId = $chapter?->subject?->curriculum?->course_id;
        } elseif ($note->noteable_type === 'App\Models\Lesson') {
            $lesson = Lesson::with('chapter.subject.curriculum')->find($note->noteable_id);
            $courseId = $lesson?->chapter?->subject?->curriculum?->course_id;
        }

        // Check if student is enrolled in the note's course (skip if course can't be determined)
        if ($courseId && !in_array($courseId, $enrolledCourseIds)) {
            abort(403, 'You must be enrolled in this course to view this note. Course: ' . $courseId);
        }

        // Load noteable for display
        $note->load('noteable');

        return view('student.note_show', compact('note'));
    }

    public function about()
    {
        return view('student.about.index');
    }
}
