<?php

namespace App\Http\Controllers;

use App\Models\LiveClass;
use App\Models\Course;
use App\Models\BTLiveSession;
use App\Models\BTLiveParticipant;
use App\Models\Student;
use App\Services\BTLive\BTLiveSessionService;
use App\Services\BTLive\BTLiveBroadcastService;
use App\Services\BTLive\BTLiveWebSocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * BTLIVE V2 - Digital Classroom Controller
 * Broadcast architecture for 1 teacher -> many students
 */
class BTLiveV2Controller extends Controller
{
    protected BTLiveSessionService $sessionService;
    protected BTLiveBroadcastService $broadcastService;
    protected BTLiveWebSocketService $wsService;

    public function __construct(
        BTLiveSessionService $sessionService,
        BTLiveBroadcastService $broadcastService,
        BTLiveWebSocketService $wsService
    ) {
        $this->sessionService = $sessionService;
        $this->broadcastService = $broadcastService;
        $this->wsService = $wsService;
    }

    /**
     * Create a new BTLIVE V2 session from curriculum
     */
    public function storeSession(Request $request, Course $course)
    {
        // Authorization
        if ($course->tenant_id !== Auth::user()->tenant_id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'chapter_id' => 'nullable|exists:chapters,id',
            'lesson_id' => 'nullable|exists:lessons,id',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:300',
            'access_code' => 'nullable|string|max:20',
            'chat_enabled' => 'boolean',
            'raise_hand_enabled' => 'boolean',
            'polls_enabled' => 'boolean',
            'recording_enabled' => 'boolean',
        ]);

        $tenant = Auth::user()->tenant;

        // Create BTLiveSession
        $session = BTLiveSession::create([
            'tenant_id' => $tenant->id,
            'course_id' => $course->id,
            'subject_id' => $validated['subject_id'] ?? null,
            'chapter_id' => $validated['chapter_id'] ?? null,
            'lesson_id' => $validated['lesson_id'] ?? null,
            'teacher_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'room_name' => $this->sessionService->generateRoomName(),
            'access_code' => $validated['access_code'] ?? null,
            'status' => 'scheduled',
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'],
            'max_participants' => 1000,
            'chat_enabled' => $validated['chat_enabled'] ?? true,
            'raise_hand_enabled' => $validated['raise_hand_enabled'] ?? true,
            'polls_enabled' => $validated['polls_enabled'] ?? true,
            'whiteboard_enabled' => true,
            'pdf_enabled' => true,
            'recording_enabled' => $validated['recording_enabled'] ?? true,
            'replay_enabled' => true,
        ]);

        return redirect()->route('tenant.curriculum.index', $course)
            ->with('success', 'BTLIVE V2 session created successfully. Click "START V2" when ready to begin.');
    }

    /**
     * Show teacher classroom interface
     */
    public function teacherRoom(BTLiveSession $session)
    {
        // Authorization
        if ($session->teacher_id !== Auth::id() && !Auth::user()->hasRole('tenant_admin')) {
            abort(403, 'Unauthorized');
        }

        // Auto-start if not started
        if ($session->status === 'scheduled') {
            $this->sessionService->startSession($session);
        }

        // Get participants
        $participants = $session->participants()->active()->with('student')->get();

        // Get PDFs
        $pdfs = $session->pdfs()->ordered()->get();

        // Get active polls
        $activePolls = $session->polls()->active()->get();

        // Get raised hands
        $raisedHands = $session->raisedHands()->active()->ordered()->get();

        // Get recent chat
        $recentChat = $session->chatMessages()
            ->visible()
            ->with('participant')
            ->orderByDesc('timestamp')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return view('btlive.v2.teacher_room', compact(
            'session',
            'participants',
            'pdfs',
            'activePolls',
            'raisedHands',
            'recentChat'
        ));
    }

    /**
     * Show student classroom interface
     */
    public function studentRoom(BTLiveSession $session, Request $request)
    {
        // Validate access code
        if ($session->access_code && $request->get('code') !== $session->access_code) {
            return view('btlive.v2.access_code', compact('session'));
        }

        // Check if session is live
        if (!$session->isLive() && !$session->isEnded()) {
            return view('btlive.v2.waiting', compact('session'));
        }

        // Get student info
        $student = null;
        $participant = null;

        if (Auth::check() && Auth::user()->hasRole('student')) {
            $student = Student::where('user_id', Auth::id())
                ->where('tenant_id', $session->tenant_id)
                ->first();

            // Check if already joined
            $participant = $session->participants()
                ->where('student_id', $student?->id)
                ->active()
                ->first();
        }

        // Get current state for joining
        $currentState = $this->broadcastService->getCurrentState($session);

        return view('btlive.v2.student_room', compact(
            'session',
            'student',
            'participant',
            'currentState'
        ));
    }

    /**
     * Student join session
     */
    public function joinSession(Request $request, BTLiveSession $session)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'nullable|string',
        ]);

        // Validate access code
        if ($session->access_code && $validated['code'] !== $session->access_code) {
            return response()->json(['error' => 'Invalid access code'], 403);
        }

        // Check if session is live
        if (!$session->isLive()) {
            return response()->json(['error' => 'Session not live'], 400);
        }

        // Get student info
        $studentId = null;
        $userId = null;

        if (Auth::check()) {
            $userId = Auth::id();
            $student = Student::where('user_id', $userId)
                ->where('tenant_id', $session->tenant_id)
                ->first();
            $studentId = $student?->id;
        }

        // Check if already joined
        if ($studentId) {
            $existing = $session->participants()
                ->where('student_id', $studentId)
                ->active()
                ->first();

            if ($existing) {
                return response()->json([
                    'participant_id' => $existing->id,
                    'state' => $this->broadcastService->getCurrentState($session),
                ]);
            }
        }

        // Add participant
        $participant = $this->sessionService->addParticipant(
            $session,
            $validated['name'],
            'student',
            $userId,
            $studentId,
            [
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ],
            $request->ip()
        );

        return response()->json([
            'participant_id' => $participant->id,
            'state' => $this->broadcastService->getCurrentState($session),
        ]);
    }

    /**
     * Handle WebSocket events (teacher)
     */
    public function handleTeacherEvent(Request $request, BTLiveSession $session)
    {
        $validated = $request->validate([
            'event_type' => 'required|string',
            'data' => 'array',
        ]);

        // Authorization
        if ($session->teacher_id !== Auth::id() && !Auth::user()->hasRole('tenant_admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $result = $this->wsService->handleTeacherEvent(
            $validated['event_type'],
            $validated['data'] ?? [],
            $session,
            Auth::id()
        );

        return response()->json($result);
    }

    /**
     * Handle WebSocket events (student)
     */
    public function handleStudentEvent(Request $request, BTLiveParticipant $participant)
    {
        $validated = $request->validate([
            'event_type' => 'required|string',
            'data' => 'array',
        ]);

        // Validate participant belongs to current user or session
        $session = $participant->session;

        $result = $this->wsService->handleStudentEvent(
            $validated['event_type'],
            $validated['data'] ?? [],
            $participant
        );

        return response()->json($result);
    }

    /**
     * Upload PDF
     */
    public function uploadPdf(Request $request, BTLiveSession $session)
    {
        $validated = $request->validate([
            'pdf' => 'required|file|mimes:pdf|max:50000',
            'title' => 'nullable|string|max:255',
        ]);

        $file = $request->file('pdf');
        $path = $file->store('btlive/pdfs/' . $session->tenant_id, 'public');

        $pdf = $session->pdfs()->create([
            'uploaded_by' => Auth::id(),
            'title' => $validated['title'] ?? $file->getClientOriginalName(),
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'total_pages' => 1, // TODO: Extract from PDF
            'display_order' => $session->pdfs()->count(),
        ]);

        return response()->json([
            'pdf_id' => $pdf->id,
            'title' => $pdf->title,
            'url' => $pdf->file_url,
        ]);
    }

    /**
     * Activate PDF
     */
    public function activatePdf(Request $request, BTLiveSession $session)
    {
        $validated = $request->validate([
            'pdf_id' => 'required|exists:btlive_pdfs,id',
            'page' => 'integer|min:1',
        ]);

        $pdf = $session->pdfs()->findOrFail($validated['pdf_id']);
        $pdf->activate();

        // Broadcast to all participants
        $broadcast = $this->broadcastService->broadcastPdfPageChange(
            $session,
            $pdf->id,
            $validated['page'] ?? 1
        );

        return response()->json([
            'pdf' => $pdf->toArray(),
            'broadcast' => $broadcast,
        ]);
    }

    /**
     * Get session state (for polling)
     */
    public function getState(BTLiveSession $session, Request $request)
    {
        $lastTimestamp = $request->get('last_timestamp', 0);

        $state = $this->broadcastService->getCurrentState($session);

        // Get new events since last timestamp
        $newEvents = $session->whiteboardEvents()
            ->where('timestamp', '>', $lastTimestamp)
            ->orderBy('timestamp')
            ->get();

        return response()->json([
            'state' => $state,
            'new_events' => $newEvents,
            'server_time' => now()->getTimestampMs(),
        ]);
    }

    /**
     * End session
     */
    public function endSession(BTLiveSession $session)
    {
        // Authorization
        if ($session->teacher_id !== Auth::id() && !Auth::user()->hasRole('tenant_admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->sessionService->endSession($session);

        return response()->json([
            'status' => 'ended',
            'message' => 'Session ended successfully',
        ]);
    }

    /**
     * Get replay data
     */
    public function getReplay(int $recordingId)
    {
        $recording = \App\Models\BTLiveRecording::with('session')
            ->where('is_approved', true)
            ->findOrFail($recordingId);

        $replayData = app(\App\Services\BTLive\BTLiveRecordingService::class)
            ->getReplayData($recordingId);

        return view('btlive.v2.replay', compact('recording', 'replayData'));
    }

    /**
     * Get replay state at timestamp (API)
     */
    public function getReplayState(Request $request, int $recordingId)
    {
        $timestamp = $request->get('timestamp', 0);

        $state = app(\App\Services\BTLive\BTLiveRecordingService::class)
            ->getReplayStateAt($recordingId, $timestamp);

        return response()->json($state);
    }
}
