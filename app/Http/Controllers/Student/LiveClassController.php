<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\LiveClass;

class LiveClassController extends Controller
{
    public function index()
    {
        $student = Auth::user();
        $tenant = app('current_tenant');

        // Get course IDs the student is enrolled in (active/approved)
        $courseIds = $student->enrollments()
            ->whereIn('enrollment_status', ['active', 'approved'])
            ->pluck('course_id');

        $now = now();

        // Upcoming: Enrolled courses OR public classes from THIS tenant only
        $upcoming = LiveClass::where(function($q) use ($courseIds, $tenant) {
                $q->whereIn('course_id', $courseIds)
                  ->orWhere(function($q2) use ($tenant) {
                      $q2->where('is_public', true)
                         ->where('tenant_id', $tenant->id);
                  });
            })
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>', $now)
            ->with('course', 'creator')
            ->orderBy('scheduled_at')
            ->get();

        // Live Now: Enrolled courses OR public classes from THIS tenant only
        $liveNow = LiveClass::where(function($q) use ($courseIds, $tenant) {
                $q->whereIn('course_id', $courseIds)
                  ->orWhere(function($q2) use ($tenant) {
                      $q2->where('is_public', true)
                         ->where('tenant_id', $tenant->id);
                  });
            })
            ->where('status', 'live')
            ->with('course', 'creator')
            ->orderBy('scheduled_at')
            ->get();

        // Past: Enrolled courses OR public classes from THIS tenant only
        $past = LiveClass::where(function($q) use ($courseIds, $tenant) {
                $q->whereIn('course_id', $courseIds)
                  ->orWhere(function($q2) use ($tenant) {
                      $q2->where('is_public', true)
                         ->where('tenant_id', $tenant->id);
                  });
            })
            ->whereIn('status', ['completed', 'cancelled'])
            ->with('course', 'creator')
            ->orderByDesc('scheduled_at')
            ->limit(30)
            ->get();

        return view('student.live_classes.index', compact('upcoming', 'liveNow', 'past'));
    }
}
