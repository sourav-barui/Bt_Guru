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

        // Get course IDs the student is enrolled in (active/approved)
        $courseIds = $student->enrollments()
            ->whereIn('enrollment_status', ['active', 'approved'])
            ->pluck('course_id');

        $now = now();

        // Upcoming: Enrolled courses OR public classes
        $upcoming = LiveClass::where(function($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds)
                  ->orWhere('is_public', true);
            })
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>', $now)
            ->with('course', 'creator')
            ->orderBy('scheduled_at')
            ->get();

        // Live Now: Enrolled courses OR public classes
        $liveNow = LiveClass::where(function($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds)
                  ->orWhere('is_public', true);
            })
            ->where('status', 'live')
            ->with('course', 'creator')
            ->orderBy('scheduled_at')
            ->get();

        // Past: Enrolled courses OR public classes
        $past = LiveClass::where(function($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds)
                  ->orWhere('is_public', true);
            })
            ->whereIn('status', ['completed', 'cancelled'])
            ->with('course', 'creator')
            ->orderByDesc('scheduled_at')
            ->limit(30)
            ->get();

        return view('student.live_classes.index', compact('upcoming', 'liveNow', 'past'));
    }
}
