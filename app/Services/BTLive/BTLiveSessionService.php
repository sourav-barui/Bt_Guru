<?php

namespace App\Services\BTLive;

use App\Models\BTLiveSession;
use App\Models\BTLiveParticipant;
use App\Models\BTLiveReplayTimeline;
use App\Models\LiveClass;
use Illuminate\Support\Str;

class BTLiveSessionService
{
    /**
     * Generate unique room name
     */
    public function generateRoomName(): string
    {
        return 'btv2-' . Str::random(12);
    }

    /**
     * Create new BTLive session from LiveClass
     */
    public function createSession(LiveClass $liveClass, int $teacherId): BTLiveSession
    {
        $roomName = $this->generateRoomName();
        
        return BTLiveSession::create([
            'tenant_id' => $liveClass->tenant_id,
            'course_id' => $liveClass->course_id,
            'subject_id' => $liveClass->subject_id,
            'chapter_id' => $liveClass->chapter_id,
            'lesson_id' => $liveClass->lesson_id,
            'live_class_id' => $liveClass->id,
            'teacher_id' => $teacherId,
            'title' => $liveClass->title,
            'description' => $liveClass->description,
            'room_name' => $roomName,
            'access_code' => Str::random(6),
            'status' => 'scheduled',
            'scheduled_at' => $liveClass->scheduled_at,
            'duration_minutes' => $liveClass->duration_minutes,
            'settings' => [
                'max_participants' => 1000,
                'auto_record' => true,
                'allow_guests' => false,
                'mute_students_on_entry' => true,
                'block_student_video' => true,
                'block_student_audio' => true,
            ],
            'chat_enabled' => true,
            'raise_hand_enabled' => true,
            'polls_enabled' => true,
            'whiteboard_enabled' => true,
            'pdf_enabled' => true,
            'recording_enabled' => true,
            'replay_enabled' => true,
        ]);
    }

    /**
     * Start the session
     */
    public function startSession(BTLiveSession $session): BTLiveSession
    {
        $session->update([
            'status' => 'live',
            'started_at' => now(),
        ]);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $session->id,
            'session_start',
            ['teacher_id' => $session->teacher_id],
            0
        );

        return $session->fresh();
    }

    /**
     * End the session
     */
    public function endSession(BTLiveSession $session): BTLiveSession
    {
        $timestamp = $this->getCurrentTimestamp($session);
        
        $session->update([
            'status' => 'ended',
            'ended_at' => now(),
            'duration_minutes' => $this->calculateDuration($session),
        ]);

        // Close all active participants
        $session->participants()->active()->update([
            'is_active' => false,
            'left_at' => now(),
        ]);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $session->id,
            'session_end',
            ['duration_ms' => $timestamp],
            $timestamp
        );

        return $session->fresh();
    }

    /**
     * Pause the session (temporarily stop broadcasting)
     */
    public function pauseSession(BTLiveSession $session): BTLiveSession
    {
        $session->update(['status' => 'paused']);
        return $session->fresh();
    }

    /**
     * Resume the session
     */
    public function resumeSession(BTLiveSession $session): BTLiveSession
    {
        $session->update(['status' => 'live']);
        return $session->fresh();
    }

    /**
     * Add participant to session
     */
    public function addParticipant(
        BTLiveSession $session,
        string $name,
        string $role = 'student',
        ?int $userId = null,
        ?int $studentId = null,
        array $deviceInfo = [],
        ?string $ipAddress = null
    ): BTLiveParticipant {
        $timestamp = $this->getCurrentTimestamp($session);

        $participant = BTLiveParticipant::create([
            'session_id' => $session->id,
            'user_id' => $userId,
            'student_id' => $studentId,
            'role' => $role,
            'name' => $name,
            'email' => $userId ? \App\Models\User::find($userId)?->email : null,
            'device_info' => $deviceInfo,
            'ip_address' => $ipAddress,
            'joined_at' => now(),
            'last_activity_at' => now(),
            'is_active' => true,
            'is_muted' => $role === 'student', // Auto-mute students
            'is_camera_off' => $role === 'student', // Auto-disable student camera
            'is_screen_blocked' => $role === 'student', // Block screen for students
            'permissions' => $this->getDefaultPermissions($role),
        ]);

        // Update participant count
        $this->updateParticipantCount($session);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $session->id,
            'participant_join',
            [
                'participant_id' => $participant->id,
                'name' => $name,
                'role' => $role,
            ],
            $timestamp
        );

        return $participant;
    }

    /**
     * Remove participant from session
     */
    public function removeParticipant(BTLiveParticipant $participant, string $reason = null): void
    {
        $session = $participant->session;
        $timestamp = $this->getCurrentTimestamp($session);

        $participant->markAsLeft();

        // Update participant count
        $this->updateParticipantCount($session);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $session->id,
            'participant_leave',
            [
                'participant_id' => $participant->id,
                'name' => $participant->name,
                'reason' => $reason,
            ],
            $timestamp
        );
    }

    /**
     * Kick participant (force remove)
     */
    public function kickParticipant(BTLiveParticipant $participant, int $kickedBy, string $reason = null): void
    {
        $this->removeParticipant($participant, 'kicked: ' . ($reason ?? 'No reason'));
    }

    /**
     * Get session by room name
     */
    public function getByRoomName(string $roomName): ?BTLiveSession
    {
        return BTLiveSession::where('room_name', $roomName)->first();
    }

    /**
     * Validate access code
     */
    public function validateAccessCode(BTLiveSession $session, string $code): bool
    {
        return $session->access_code === $code;
    }

    /**
     * Update participant count
     */
    private function updateParticipantCount(BTLiveSession $session): void
    {
        $count = $session->participants()->active()->count();
        $session->update(['participant_count' => $count]);
    }

    /**
     * Calculate session duration in minutes
     */
    private function calculateDuration(BTLiveSession $session): int
    {
        if (!$session->started_at) {
            return 0;
        }
        $end = $session->ended_at ?? now();
        return $session->started_at->diffInMinutes($end);
    }

    /**
     * Get current timestamp in ms from session start
     */
    private function getCurrentTimestamp(BTLiveSession $session): int
    {
        if (!$session->started_at) {
            return 0;
        }
        return now()->diffInMilliseconds($session->started_at);
    }

    /**
     * Get default permissions based on role
     */
    private function getDefaultPermissions(string $role): array
    {
        return match ($role) {
            'teacher' => [
                'can_chat' => true,
                'can_share_video' => true,
                'can_share_audio' => true,
                'can_upload_pdf' => true,
                'can_annotate' => true,
                'can_create_poll' => true,
                'can_manage_participants' => true,
                'can_mute_all' => true,
                'can_block_students' => true,
                'can_record' => true,
            ],
            'moderator' => [
                'can_chat' => true,
                'can_manage_participants' => true,
                'can_mute_all' => true,
                'can_moderate_chat' => true,
            ],
            'student' => [
                'can_chat' => true,
                'can_raise_hand' => true,
                'can_answer_poll' => true,
                'can_view_pdf' => true,
                'can_see_annotations' => true,
            ],
            'guest' => [
                'can_chat' => false,
                'can_view_pdf' => true,
                'can_see_annotations' => true,
            ],
            default => ['can_chat' => true],
        };
    }
}
