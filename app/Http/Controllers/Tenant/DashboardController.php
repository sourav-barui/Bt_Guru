<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;

        $stats = [
            'total_students' => User::where('tenant_id', $tenant->id)
                ->whereHas('roles', function ($q) { $q->where('name', 'student'); })
                ->count(),
            'active_courses' => Course::where('tenant_id', $tenant->id)
                ->where('status', 'active')
                ->count(),
            'total_teachers' => User::where('tenant_id', $tenant->id)
                ->whereHas('roles', function ($q) { $q->where('name', 'teacher'); })
                ->count(),
            'pending_admissions' => Enrollment::where('tenant_id', $tenant->id)
                ->where('enrollment_status', 'pending')
                ->count(),
            'total_enrollments' => Enrollment::where('tenant_id', $tenant->id)->count(),
            'active_enrollments' => Enrollment::where('tenant_id', $tenant->id)
                ->where('enrollment_status', 'active')
                ->count(),
        ];

        $recentEnrollments = Enrollment::where('tenant_id', $tenant->id)
            ->latest()
            ->take(5)
            ->with(['student', 'course'])
            ->get();

        $recentNotices = \App\Models\Notice::where('tenant_id', $tenant->id)
            ->active()
            ->latest()
            ->take(5)
            ->get();

        return view('tenant.dashboard', compact('stats', 'recentEnrollments', 'recentNotices'));
    }
}
