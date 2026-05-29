<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        $teacher->load('taughtCourses');

        $stats = [
            'assigned_courses' => $teacher->taughtCourses()->count(),
            'total_students' => $teacher->taughtCourses()
                ->withCount('enrollments')
                ->get()
                ->sum('enrollments_count'),
        ];

        $assignedCourses = $teacher->taughtCourses()
            ->with(['enrollments.student'])
            ->get();

        $notices = \App\Models\Notice::where('tenant_id', $teacher->tenant_id)
            ->active()
            ->forTeachers()
            ->latest()
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact('stats', 'assignedCourses', 'notices'));
    }

    public function myCourses()
    {
        $teacher = Auth::user();
        
        $courses = $teacher->taughtCourses()
            ->withCount('enrollments')
            ->with(['enrollments' => function ($q) {
                $q->where('enrollment_status', 'active')
                  ->with('student');
            }])
            ->get();
        
        $totalStudents = $courses->sum('enrollments_count');
        
        return view('teacher.courses.index', compact('courses', 'totalStudents'));
    }

    public function showCourse(Course $course)
    {
        $teacher = Auth::user();
        
        // Verify this teacher is assigned to this course
        if (!$course->teachers()->where('teacher_id', $teacher->id)->exists()) {
            abort(403, 'You are not assigned to this course.');
        }
        
        $course->load([
            'enrollments.student',
            'curricula.subjects.contents.user',
            'curricula.subjects.notes.user',
            'curricula.subjects.liveClasses.creator',
            'curricula.subjects.chapters.contents.user',
            'curricula.subjects.chapters.notes.user',
            'curricula.subjects.chapters.liveClasses.creator',
            'curricula.subjects.chapters.lessons.contents.user',
            'curricula.subjects.chapters.lessons.notes.user',
            'curricula.subjects.chapters.lessons.liveClasses.creator',
        ]);
        
        $students = $course->enrollments()
            ->where('enrollment_status', 'active')
            ->with('student')
            ->get();
        
        return view('teacher.courses.show', compact('course', 'students'));
    }
}
