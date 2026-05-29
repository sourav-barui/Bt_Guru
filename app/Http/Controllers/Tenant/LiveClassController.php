<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\LiveClass;
use App\Services\NotificationService;

class LiveClassController extends Controller
{
    public function index(Course $course)
    {
        $this->authorizeCourse($course);

        $liveClasses = LiveClass::where('course_id', $course->id)
            ->with('creator')
            ->orderByDesc('scheduled_at')
            ->get();

        return view('tenant.live_classes.index', compact('course', 'liveClasses'));
    }

    public function create(Course $course)
    {
        $this->authorizeCourse($course);
        return view('tenant.live_classes.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'platform'         => 'required|in:google_meet,zoom,ms_teams,jitsi,other',
            'meeting_url'      => 'required|url',
            'meeting_id'       => 'nullable|string|max:100',
            'meeting_password' => 'nullable|string|max:100',
            'scheduled_at'     => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'recurrence'       => 'required|in:none,daily,weekly',
            'status'           => 'required|in:scheduled,live,completed,cancelled',
            'is_public'        => 'nullable|boolean',
            'subject_id'       => 'nullable|integer|exists:subjects,id',
            'chapter_id'       => 'nullable|integer|exists:chapters,id',
            'lesson_id'        => 'nullable|integer|exists:lessons,id',
        ]);

        $liveClass = LiveClass::create(array_merge($validated, [
            'tenant_id'  => Auth::user()->tenant_id,
            'course_id'  => $course->id,
            'created_by' => Auth::id(),
        ]));

        try {
            $liveClass->load('course');
            
            // For public classes, notify all tenant students
            if ($liveClass->is_public) {
                $students = \App\Models\User::where('tenant_id', $liveClass->tenant_id)
                    ->whereHas('roles', fn($q) => $q->where('name', 'student'))
                    ->get();
                
                (new NotificationService())->send(
                    Auth::user()->tenant,
                    $students,
                    type: 'live_class',
                    title: "🎥 Public Live Class: {$liveClass->title}",
                    body: "A public live class has been scheduled — open to all students",
                    icon: 'video',
                    url: '/student/live-classes',
                    sendEmail: true
                );
            } else {
                (new NotificationService())->liveClassScheduled(Auth::user()->tenant, $liveClass);
            }
        } catch (\Throwable $e) {
            \Log::warning('Live class notification failed: ' . $e->getMessage());
        }

        // If came from curriculum page, go back there
        if ($request->input('redirect') === 'curriculum') {
            return redirect()->route('tenant.curriculum.index', $course)
                ->with('success', 'Live class scheduled successfully.');
        }

        return redirect()->route('tenant.live_classes.index', $course)
            ->with('success', 'Live class scheduled successfully.');
    }

    public function edit(Course $course, LiveClass $liveClass)
    {
        $this->authorizeCourse($course);
        return view('tenant.live_classes.edit', compact('course', 'liveClass'));
    }

    public function update(Request $request, Course $course, LiveClass $liveClass)
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'description'      => 'nullable|string|max:1000',
            'platform'         => 'required|in:google_meet,zoom,ms_teams,jitsi,other',
            'meeting_url'      => 'required|url',
            'meeting_id'       => 'nullable|string|max:100',
            'meeting_password' => 'nullable|string|max:100',
            'scheduled_at'     => 'required|date',
            'duration_minutes' => 'required|integer|min:5|max:480',
            'recurrence'       => 'required|in:none,daily,weekly',
            'status'           => 'required|in:scheduled,live,completed,cancelled',
            'is_public'        => 'nullable|boolean',
        ]);

        $liveClass->update($validated);

        return redirect()->route('tenant.live_classes.index', $course)
            ->with('success', 'Live class updated successfully.');
    }

    public function destroy(Course $course, LiveClass $liveClass)
    {
        $this->authorizeCourse($course);
        $liveClass->delete();

        return redirect()->route('tenant.live_classes.index', $course)
            ->with('success', 'Live class deleted.');
    }

    public function markLive(Course $course, LiveClass $liveClass)
    {
        $this->authorizeCourse($course);
        $liveClass->update(['status' => 'live']);
        
        // Send notification to enrolled students
        try {
            $liveClass->load('course');
            (new NotificationService())->liveClassStarted(Auth::user()->tenant, $liveClass);
        } catch (\Throwable $e) {
            \Log::warning('Live class start notification failed: ' . $e->getMessage());
        }
        
        // Redirect to meeting URL if available
        if ($liveClass->meeting_url) {
            return redirect()->away($liveClass->meeting_url);
        }
        
        return back()->with('success', 'Class marked as Live.');
    }

    public function markCompleted(Course $course, LiveClass $liveClass)
    {
        $this->authorizeCourse($course);
        $liveClass->update(['status' => 'completed']);
        return back()->with('success', 'Class marked as Completed.');
    }

    public function endLive(Course $course, LiveClass $liveClass)
    {
        $this->authorizeCourse($course);
        $liveClass->update(['status' => 'completed']);
        
        // Send notification to students that class has ended
        try {
            $liveClass->load('course');
            
            // For public classes, notify all students. Otherwise, only enrolled students.
            $studentsQuery = \App\Models\User::where('tenant_id', $liveClass->tenant_id)
                ->whereHas('roles', fn($q) => $q->where('name', 'student'));
            
            if (!$liveClass->is_public) {
                $studentsQuery->whereHas('enrollments', fn($q) => $q->where('course_id', $liveClass->course_id)->where('enrollment_status', 'active'));
            }
            
            $students = $studentsQuery->get();
            
            (new NotificationService())->send(
                Auth::user()->tenant,
                $students,
                type: 'live_class',
                title: "✅ Class Ended: {$liveClass->title}",
                body: "The live class has ended. Recorded video will be available soon — {$liveClass->course->title}",
                icon: 'video',
                url: '/student/live-classes',
                sendEmail: true
            );
        } catch (\Throwable $e) {
            \Log::warning('Live class end notification failed: ' . $e->getMessage());
        }
        
        return back()->with('success', 'Live class ended. You can now upload the recorded video.');
    }

    public function uploadVideo(Request $request, Course $course, LiveClass $liveClass)
    {
        $this->authorizeCourse($course);
        
        $validated = $request->validate([
            'video_url' => 'required|url',
        ]);
        
        $liveClass->update(['video_url' => $validated['video_url']]);
        
        // Send notification to students that video is available
        try {
            $liveClass->load('course');
            
            // For public classes, notify all students. Otherwise, only enrolled students.
            $studentsQuery = \App\Models\User::where('tenant_id', $liveClass->tenant_id)
                ->whereHas('roles', fn($q) => $q->where('name', 'student'));
            
            if (!$liveClass->is_public) {
                $studentsQuery->whereHas('enrollments', fn($q) => $q->where('course_id', $liveClass->course_id)->where('enrollment_status', 'active'));
            }
            
            $students = $studentsQuery->get();
            
            (new NotificationService())->send(
                Auth::user()->tenant,
                $students,
                type: 'video',
                title: "📹 Recorded: {$liveClass->title}",
                body: "The recorded video is now available — {$liveClass->course->title}",
                icon: 'video',
                url: $validated['video_url'],
                sendEmail: true
            );
        } catch (\Throwable $e) {
            \Log::warning('Video upload notification failed: ' . $e->getMessage());
        }
        
        return back()->with('success', 'Video link uploaded and students notified.');
    }

    private function authorizeCourse(Course $course): void
    {
        if ($course->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }
    }
}
