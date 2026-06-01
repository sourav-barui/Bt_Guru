<?php

namespace App\Http\Controllers;

use App\Models\LiveClass;
use App\Models\Course;
use App\Services\BTLiveService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BTLiveController extends Controller
{
    protected BTLiveService $btliveService;
    
    public function __construct(BTLiveService $btliveService)
    {
        $this->btliveService = $btliveService;
    }
    
    /**
     * Show form to create new BTLive class
     * GET /btlive/create/{course}
     */
    public function create(Course $course)
    {
        $this->authorizeCourse($course);
        
        // Get subjects through curricula (course -> curricula -> subjects)
        $subjects = $course->curricula()
            ->with('subjects')
            ->get()
            ->pluck('subjects')
            ->flatten()
            ->pluck('name', 'id');
        
        return view('btlive.create', compact('course', 'subjects'));
    }
    
    /**
     * Store new BTLive class
     * POST /btlive/store/{course}
     */
    public function store(Request $request, Course $course)
    {
        $this->authorizeCourse($course);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:300',
            'is_public' => 'boolean',
            'btlive_lobby_enabled' => 'boolean',
            'btlive_chat_enabled' => 'boolean',
            'btlive_teacher_only_video' => 'boolean',
        ]);
        
        $tenant = Auth::user()->tenant;
        
        // Create LiveClass first (without room name)
        $liveClass = LiveClass::create([
            'tenant_id' => $tenant->id,
            'course_id' => $course->id,
            'subject_id' => $validated['subject_id'] ?? null,
            'chapter_id' => $validated['chapter_id'] ?? null,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'platform' => 'jitsi',
            'meeting_url' => '', // Will update after room name generation
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'],
            'status' => 'scheduled',
            'is_public' => $validated['is_public'] ?? false,
            // BTLive settings
            'is_btlive' => true,
            'btlive_room_name' => '', // Will update after generation
            'btlive_lobby_enabled' => $validated['btlive_lobby_enabled'] ?? true,
            'btlive_waiting_room_enabled' => true,
            'btlive_chat_enabled' => $validated['btlive_chat_enabled'] ?? true,
            'btlive_teacher_only_video' => $validated['btlive_teacher_only_video'] ?? true,
            'btlive_teacher_only_audio' => true,
            'btlive_attendance_enabled' => true,
            'btlive_jwt_required' => config('btlive.require_jwt', true),
        ]);
        
        // Now generate room name with the created class
        $roomName = $this->btliveService->generateRoomName($liveClass);
        
        // Update with room name and meeting URL
        $liveClass->update([
            'btlive_room_name' => $roomName,
            'meeting_url' => 'https://' . config('btlive.jitsi_domain', 'meet.jit.si') . '/' . $roomName,
        ]);
        
        // Notify enrolled students
        $this->notifyStudentsClassScheduled($liveClass);
        
        return redirect()->route('tenant.live_classes.index', $course)
            ->with('success', 'BTLive class scheduled successfully!');
    }
    
    /**
     * Teacher Classroom View
     * GET /btlive/{liveClass}/room
     */
    public function teacherRoom(LiveClass $liveClass)
    {
        $this->authorizeClass($liveClass);
        
        $teacher = Auth::user();
        
        // Ensure this is a BTLive class
        if (!$liveClass->is_btlive) {
            return redirect()->route('tenant.live_classes.index', $liveClass->course)
                ->with('error', 'This is not a BTLive class.');
        }
        
        // Generate moderator token
        $jwt = $this->btliveService->generateModeratorToken($liveClass, $teacher);
        
        // Get Jitsi config
        $jitsiConfig = $this->btliveService->getJitsiConfig($liveClass, $teacher, true);
        
        // Start session if not already started
        if (!$liveClass->btlive_started_at) {
            $this->btliveService->startSession($liveClass);
            
            // Notify students
            try {
                $this->notifyStudentsClassStarted($liveClass);
            } catch (\Throwable $e) {
                Log::warning('BTLive start notification failed: ' . $e->getMessage());
            }
        }
        
        // Get attendance stats
        $attendanceStats = $this->btliveService->getAttendanceStats($liveClass);
        
        return view('btlive.teacher_room', compact(
            'liveClass',
            'jwt',
            'jitsiConfig',
            'attendanceStats'
        ));
    }
    
    /**
     * Student Join View
     * GET /student/btlive/{liveClass}/join
     */
    public function studentRoom(LiveClass $liveClass)
    {
        $student = Auth::user();
        
        // Check if student can access this class
        if (!$this->canStudentAccess($liveClass, $student)) {
            abort(403, 'You are not authorized to join this class.');
        }
        
        // Ensure this is a BTLive class
        if (!$liveClass->is_btlive) {
            return redirect()->route('student.live_classes.index')
                ->with('error', 'This class is not available for BTLive.');
        }
        
        // Check if class is live or scheduled
        if (!in_array($liveClass->status, ['live', 'scheduled'])) {
            return redirect()->route('student.live_classes.index')
                ->with('error', 'This class is not currently active.');
        }
        
        // Generate student token
        $jwt = $this->btliveService->generateStudentToken($liveClass, $student);
        
        // Get Jitsi config
        $jitsiConfig = $this->btliveService->getJitsiConfig($liveClass, $student, false);
        
        // Record attendance (join)
        $this->recordAttendance($liveClass, $student, 'join');
        
        return view('btlive.student_room', compact(
            'liveClass',
            'jwt',
            'jitsiConfig'
        ));
    }
    
    /**
     * Student Leave - Record attendance when student leaves
     * POST /student/btlive/{liveClass}/leave
     */
    public function studentLeave(LiveClass $liveClass)
    {
        $student = Auth::user();
        
        // Check if student can access
        if (!$this->canStudentAccess($liveClass, $student)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        // Record leave attendance
        $this->recordAttendance($liveClass, $student, 'leave');
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Attendance View (Teacher)
     * GET /btlive/{liveClass}/attendance
     */
    public function attendance(LiveClass $liveClass)
    {
        $this->authorizeClass($liveClass);
        
        $attendance = $liveClass->attendance()
            ->with('student')
            ->orderBy('joined_at', 'desc')
            ->paginate(50);
            
        $stats = $this->btliveService->getAttendanceStats($liveClass);
        
        return view('btlive.attendance', compact('liveClass', 'attendance', 'stats'));
    }
    
    /**
     * End Meeting (Teacher)
     * POST /btlive/{liveClass}/end
     */
    public function endMeeting(LiveClass $liveClass)
    {
        $this->authorizeClass($liveClass);
        
        if (!$liveClass->is_btlive) {
            return response()->json(['error' => 'Not a BTLive class'], 400);
        }
        
        // End the session
        $this->btliveService->endSession($liveClass);
        
        // Notify students
        try {
            $this->notifyStudentsClassEnded($liveClass);
        } catch (\Throwable $e) {
            Log::warning('BTLive end notification failed: ' . $e->getMessage());
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Meeting ended successfully',
            'redirect' => route('tenant.live_classes.index', $liveClass->course),
        ]);
    }
    
    /**
     * Recording Webhook
     * POST /btlive/{liveClass}/recording-webhook
     */
    public function recordingWebhook(Request $request, LiveClass $liveClass)
    {
        // Verify webhook signature if configured
        $webhookSecret = config('btlive.webhook_secret');
        if ($webhookSecret) {
            $signature = $request->header('X-Jitsi-Signature');
            $expected = hash_hmac('sha256', $request->getContent(), $webhookSecret);
            
            if (!hash_equals($expected, $signature)) {
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }
        
        $data = $request->all();
        
        $this->btliveService->handleRecordingWebhook($liveClass, $data);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Attendance Webhook (from Jitsi events)
     * POST /btlive/{liveClass}/attendance-webhook
     */
    public function attendanceWebhook(Request $request, LiveClass $liveClass)
    {
        $event = $request->input('event');
        $participant = $request->input('participant');
        
        Log::info('BTLive attendance webhook', [
            'live_class_id' => $liveClass->id,
            'event' => $event,
            'participant' => $participant,
        ]);
        
        if ($event === 'participant_joined' && isset($participant['email'])) {
            // Find student by email
            $student = \App\Models\User::where('email', $participant['email'])->first();
            
            if ($student) {
                $this->recordAttendance($liveClass, $student, 'join', [
                    'jitsi_participant_id' => $participant['id'] ?? null,
                    'display_name' => $participant['displayName'] ?? null,
                ]);
            }
        }
        
        if ($event === 'participant_left' && isset($participant['email'])) {
            $student = \App\Models\User::where('email', $participant['email'])->first();
            
            if ($student) {
                $this->recordAttendance($liveClass, $student, 'leave');
            }
        }
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Kick Participant (Teacher)
     * POST /btlive/{liveClass}/kick-participant
     */
    public function kickParticipant(Request $request, LiveClass $liveClass)
    {
        $this->authorizeClass($liveClass);
        
        $validated = $request->validate([
            'participant_id' => 'required|string',
            'reason' => 'nullable|string|max:255',
        ]);
        
        $success = $this->btliveService->kickParticipant(
            $liveClass,
            $validated['participant_id'],
            $validated['reason'] ?? ''
        );
        
        return response()->json([
            'success' => $success,
            'message' => $success ? 'Participant removed' : 'Failed to remove participant',
        ]);
    }
    
    /**
     * Mute All Students (Teacher)
     * POST /btlive/{liveClass}/mute-all
     */
    public function muteAll(Request $request, LiveClass $liveClass)
    {
        $this->authorizeClass($liveClass);
        
        // In real implementation, this would call Jitsi API
        // For now, we just log the action
        Log::info('BTLive mute all', ['live_class_id' => $liveClass->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'All students muted',
        ]);
    }
    
    /**
     * Get Live Attendance Stats (AJAX)
     * GET /btlive/{liveClass}/live-stats
     */
    public function liveStats(LiveClass $liveClass)
    {
        $this->authorizeClass($liveClass);
        
        $stats = $this->btliveService->getAttendanceStats($liveClass);
        $recentJoins = $liveClass->attendance()
            ->with('student:id,name,avatar')
            ->where('joined_at', '>=', now()->subMinutes(5))
            ->get();
        
        return response()->json([
            'stats' => $stats,
            'recent_joins' => $recentJoins,
            'is_live' => $liveClass->status === 'live',
        ]);
    }
    
    /**
     * Create BTLive class from existing LiveClass
     * This converts a scheduled external meeting to BTLive
     */
    public function convertToBTLive(Request $request, LiveClass $liveClass)
    {
        $this->authorizeClass($liveClass);
        
        if ($liveClass->is_btlive) {
            return response()->json(['error' => 'Already a BTLive class'], 400);
        }
        
        // Generate room name
        $roomName = $this->btliveService->generateRoomName($liveClass);
        
        $liveClass->update([
            'is_btlive' => true,
            'platform' => 'jitsi',
            'btlive_room_name' => $roomName,
            'btlive_lobby_enabled' => true,
            'btlive_waiting_room_enabled' => true,
            'btlive_chat_enabled' => true,
            'btlive_teacher_only_video' => true,
            'btlive_teacher_only_audio' => true,
            'btlive_attendance_enabled' => true,
            'btlive_jwt_required' => true,
        ]);
        
        return response()->json([
            'success' => true,
            'room_name' => $roomName,
            'teacher_url' => route('btlive.teacher_room', $liveClass),
        ]);
    }
    
    /**
     * Helper: Check if user can access this class
     */
    protected function authorizeClass(LiveClass $liveClass): void
    {
        $user = Auth::user();
        
        // Must be same tenant
        if ($liveClass->tenant_id !== $user->tenant_id) {
            abort(403, 'Unauthorized tenant access');
        }
        
        // Must be teacher/admin of this course
        $isAuthorized = $liveClass->course->teachers->contains($user) 
            || $user->hasRole('tenant_admin');
            
        if (!$isAuthorized) {
            abort(403, 'Not authorized for this course');
        }
    }
    
    /**
     * Helper: Check student access
     */
    protected function canStudentAccess(LiveClass $liveClass, $student): bool
    {
        // Same tenant check
        if ($liveClass->tenant_id !== $student->tenant_id) {
            return false;
        }
        
        // Public class - any student of tenant can join
        if ($liveClass->is_public) {
            return $student->hasRole('student');
        }
        
        // Private class - must be enrolled
        $isEnrolled = $student->enrollments()
            ->where('course_id', $liveClass->course_id)
            ->whereIn('enrollment_status', ['active', 'approved'])
            ->exists();
            
        return $isEnrolled;
    }
    
    /**
     * Record attendance
     */
    protected function recordAttendance(LiveClass $liveClass, $student, string $action, array $extra = []): void
    {
        if (!$liveClass->btlive_attendance_enabled) {
            return;
        }
        
        if ($action === 'join') {
            // Check if already has active session
            $existing = $liveClass->attendance()
                ->where('student_id', $student->id)
                ->whereNull('left_at')
                ->first();
                
            if ($existing) {
                return; // Already counted
            }
            
            // Create new attendance record
            $liveClass->attendance()->create([
                'tenant_id' => $liveClass->tenant_id,
                'student_id' => $student->id,
                'joined_at' => now(),
                'ip_address' => request()->ip(),
                'device_type' => $this->detectDevice(),
                'browser' => $this->detectBrowser(),
                'os' => $this->detectOS(),
                'jitsi_participant_id' => $extra['jitsi_participant_id'] ?? null,
                'display_name' => $extra['display_name'] ?? $student->name,
            ]);
        }
        
        if ($action === 'leave') {
            $attendance = $liveClass->attendance()
                ->where('student_id', $student->id)
                ->whereNull('left_at')
                ->first();
                
            if ($attendance) {
                $attendance->update([
                    'left_at' => now(),
                    'duration_seconds' => now()->diffInSeconds($attendance->joined_at),
                ]);
            }
        }
    }
    
    /**
     * Send notifications when class starts
     */
    protected function notifyStudentsClassStarted(LiveClass $liveClass): void
    {
        $service = new NotificationService();
        
        if ($liveClass->is_public) {
            // Notify all tenant students
            $students = \App\Models\User::where('tenant_id', $liveClass->tenant_id)
                ->whereHas('roles', fn($q) => $q->where('name', 'student'))
                ->get();
        } else {
            // Notify enrolled students only
            $students = \App\Models\User::where('tenant_id', $liveClass->tenant_id)
                ->whereHas('enrollments', fn($q) => $q
                    ->where('course_id', $liveClass->course_id)
                    ->whereIn('enrollment_status', ['active', 'approved'])
                )
                ->get();
        }
        
        $service->send(
            $liveClass->tenant,
            $students,
            type: 'live_class',
            title: "🔴 LIVE NOW: {$liveClass->title}",
            body: "Your class is starting now! Join BTLive classroom — {$liveClass->course->title}",
            icon: 'video',
            url: '/student/btlive/' . $liveClass->id . '/join',
            sendEmail: true
        );
    }
    
    /**
     * Send notifications when class ends
     */
    protected function notifyStudentsClassEnded(LiveClass $liveClass): void
    {
        $service = new NotificationService();
        
        $students = \App\Models\User::where('tenant_id', $liveClass->tenant_id)
            ->whereHas('enrollments', fn($q) => $q
                ->where('course_id', $liveClass->course_id)
                ->whereIn('enrollment_status', ['active', 'approved'])
            )
            ->get();
        
        $service->send(
            $liveClass->tenant,
            $students,
            type: 'live_class',
            title: "✅ Class Ended: {$liveClass->title}",
            body: "The live class has ended. Recording will be available soon.",
            icon: 'video',
            url: '/student/live-classes',
            sendEmail: true
        );
    }
    
    /**
     * Device detection helpers
     */
    protected function detectDevice(): string
    {
        $ua = request()->userAgent();
        if (str_contains($ua, 'Mobile')) return 'mobile';
        if (str_contains($ua, 'Tablet')) return 'tablet';
        return 'desktop';
    }
    
    protected function detectBrowser(): string
    {
        $ua = request()->userAgent();
        if (str_contains($ua, 'Chrome')) return 'Chrome';
        if (str_contains($ua, 'Firefox')) return 'Firefox';
        if (str_contains($ua, 'Safari')) return 'Safari';
        if (str_contains($ua, 'Edge')) return 'Edge';
        return 'Other';
    }
    
    protected function detectOS(): string
    {
        $ua = request()->userAgent();
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Mac')) return 'macOS';
        if (str_contains($ua, 'Linux')) return 'Linux';
        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'iOS') || str_contains($ua, 'iPhone')) return 'iOS';
        return 'Other';
    }
    
    /**
     * Authorize user can manage course
     */
    protected function authorizeCourse(Course $course): void
    {
        $user = Auth::user();
        
        if ($user->hasRole('super_admin')) {
            return;
        }
        
        if ($user->tenant_id !== $course->tenant_id) {
            abort(403, 'Unauthorized access to this course.');
        }
        
        if (!$user->hasAnyRole(['tenant_admin', 'teacher'])) {
            abort(403, 'Only admins and teachers can create live classes.');
        }
    }
    
    /**
     * Send notifications when class is scheduled
     */
    protected function notifyStudentsClassScheduled(LiveClass $liveClass): void
    {
        $service = new NotificationService();
        
        if ($liveClass->is_public) {
            $students = \App\Models\User::where('tenant_id', $liveClass->tenant_id)
                ->whereHas('roles', fn($q) => $q->where('name', 'student'))
                ->get();
        } else {
            $students = \App\Models\User::where('tenant_id', $liveClass->tenant_id)
                ->whereHas('enrollments', fn($q) => $q
                    ->where('course_id', $liveClass->course_id)
                    ->whereIn('enrollment_status', ['active', 'approved'])
                )
                ->get();
        }
        
        $scheduledTime = $liveClass->scheduled_at->format('D, M d \a\t h:i A');
        
        $service->send(
            $liveClass->tenant,
            $students,
            type: 'live_class',
            title: "📅 Class Scheduled: {$liveClass->title}",
            body: "New BTLive class on {$scheduledTime} — {$liveClass->course->title}",
            icon: 'calendar',
            url: '/student/live-classes',
            sendEmail: true
        );
    }
}
