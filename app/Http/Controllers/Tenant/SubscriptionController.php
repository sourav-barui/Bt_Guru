<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourseSubscription;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\Coupon;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $subscriptions = CourseSubscription::where('tenant_id', $tenantId)
            ->with(['student', 'course'])
            ->when($request->course_id, fn($q) => $q->where('course_id', $request->course_id))
            ->when($request->student_id, fn($q) => $q->where('student_id', $request->student_id))
            ->latest()
            ->paginate(20);

        $courses = Course::where('tenant_id', $tenantId)->where('fees_type', 'monthly')->get();
        $students = User::where('tenant_id', $tenantId)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->get();

        return view('tenant.subscriptions.index', compact('subscriptions', 'courses', 'students'));
    }

    public function create(Request $request)
    {
        $tenantId = Auth::user()->tenant_id;

        $courses = Course::where('tenant_id', $tenantId)->where('fees_type', 'monthly')->get();
        $students = User::where('tenant_id', $tenantId)
            ->whereHas('roles', fn($q) => $q->where('name', 'student'))
            ->get();

        $selectedCourse = $request->course_id ? Course::find($request->course_id) : null;

        return view('tenant.subscriptions.create', compact('courses', 'students', 'selectedCourse'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id'     => 'required|exists:users,id',
            'course_id'      => 'required|exists:courses,id',
            'type'           => 'required|in:current,past',
            'access_start'   => 'required|date',
            'fee_paid'       => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,paid',
            'remarks'        => 'nullable|string',
        ]);

        $tenantId = Auth::user()->tenant_id;
        $accessStart = Carbon::parse($request->access_start);
        $accessEnd   = $accessStart->copy()->addDays(29);

        // Find the enrollment
        $enrollment = Enrollment::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->where('tenant_id', $tenantId)
            ->first();

        if (!$enrollment) {
            return back()->withErrors(['student_id' => 'Student is not enrolled in this course.'])->withInput();
        }

        // Prevent duplicate subscription for same window
        $exists = CourseSubscription::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->where('access_start', $accessStart->toDateString())
            ->exists();

        if ($exists) {
            return back()->withErrors(['access_start' => 'A subscription for this month already exists for this student.'])->withInput();
        }

        CourseSubscription::create([
            'tenant_id'      => $tenantId,
            'enrollment_id'  => $enrollment->id,
            'student_id'     => $request->student_id,
            'course_id'      => $request->course_id,
            'type'           => $request->type,
            'access_start'   => $accessStart->toDateString(),
            'access_end'     => $accessEnd->toDateString(),
            'fee_paid'       => $request->fee_paid,
            'payment_status' => $request->payment_status,
            'remarks'        => $request->remarks,
            'created_by'     => Auth::id(),
        ]);

        return redirect()->route('tenant.subscriptions.index')
            ->with('success', 'Subscription added successfully.');
    }

    public function updateStatus(Request $request, CourseSubscription $subscription)
    {
        $request->validate(['payment_status' => 'required|in:pending,paid']);
        $subscription->update(['payment_status' => $request->payment_status]);
        return back()->with('success', 'Payment status updated.');
    }

    public function destroy(CourseSubscription $subscription)
    {
        $subscription->delete();
        return back()->with('success', 'Subscription removed.');
    }

    public function getCourseInfo(Course $course)
    {
        return response()->json([
            'monthly_fee'    => $course->fees,
            'past_month_fee' => $course->past_month_fee,
        ]);
    }

    // Platform Subscription Methods

    public function platformPlans()
    {
        $tenant = Auth::user()->tenant;
        $currentSubscription = $tenant->subscriptions()->latest()->first();
        $plans = SubscriptionPlan::active()->ordered()->get();

        return view('tenant.subscriptions.platform_plans', compact('plans', 'currentSubscription'));
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $coupon = Coupon::where('code', strtoupper($request->code))
            ->active()
            ->validForPlan($request->plan_id)
            ->hasUsesRemaining()
            ->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid or expired coupon code.',
            ], 400);
        }

        $plan = SubscriptionPlan::find($request->plan_id);
        $discount = $coupon->apply($plan->price);

        return response()->json([
            'valid' => true,
            'coupon' => [
                'code' => $coupon->code_upper,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
            ],
            'pricing' => $discount,
        ]);
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'coupon_code' => 'nullable|string',
        ]);

        $tenant = Auth::user()->tenant;
        $plan = SubscriptionPlan::find($request->plan_id);

        // Check if tenant already has an active subscription
        $existingSubscription = $tenant->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();

        if ($existingSubscription) {
            return back()->with('error', 'You already have an active subscription.');
        }

        // Calculate pricing
        $originalPrice = $plan->price;
        $discountAmount = 0;
        $couponCodeUsed = null;

        if ($request->coupon_code) {
            $coupon = Coupon::where('code', strtoupper($request->coupon_code))
                ->active()
                ->validForPlan($plan->id)
                ->hasUsesRemaining()
                ->first();

            if ($coupon) {
                $discountAmount = $coupon->calculateDiscount($originalPrice);
                $couponCodeUsed = $coupon->code_upper;
                $coupon->incrementUsage();
            }
        }

        $finalPrice = max(0, $originalPrice - $discountAmount);

        // Create subscription
        $subscription = Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays($plan->duration_days),
            'trial_end_date' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : null,
            'status' => $plan->trial_days > 0 ? 'trial' : 'pending',
            'coupon_code_used' => $couponCodeUsed,
            'original_price' => $originalPrice,
            'discount_amount' => $discountAmount,
            'final_price' => $finalPrice,
            'payment_status' => 'pending',
        ]);

        // Redirect to payment selection
        return redirect()->route('tenant.subscriptions.payments.select_method', $subscription->id);
    }

    public function currentSubscription()
    {
        $tenant = Auth::user()->tenant;
        $subscription = $tenant->subscriptions()->latest()->first();

        if (!$subscription) {
            return redirect()->route('tenant.subscriptions.platform_plans');
        }

        return view('tenant.subscriptions.current', compact('subscription'));
    }
}
