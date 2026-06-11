<?php

namespace App\Services\BTLive;

use App\Models\BTLiveSession;
use App\Models\BTLiveRecording;
use App\Models\BTLiveReplayTimeline;
use Illuminate\Support\Facades\Storage;

/**
 * Service for recording and replay functionality
 */
class BTLiveRecordingService
{
    /**
     * Start recording a session
     */
    public function startRecording(BTLiveSession $session): BTLiveRecording
    {
        return BTLiveRecording::create([
            'session_id' => $session->id,
            'tenant_id' => $session->tenant_id,
            'status' => 'recording',
            'started_at' => now(),
            'participant_count' => $session->participant_count,
        ]);
    }

    /**
     * Stop recording and process
     */
    public function stopRecording(BTLiveRecording $recording, ?string $teacherVideoPath = null): BTLiveRecording
    {
        $recording->update([
            'status' => 'processing',
            'ended_at' => now(),
            'teacher_video_path' => $teacherVideoPath,
            'duration_seconds' => $this->calculateDuration($recording),
        ]);

        // Build timeline from session events
        $this->buildRecordingTimeline($recording);

        // Export session data
        $this->exportSessionData($recording);

        $recording->update(['status' => 'completed']);

        return $recording->fresh();
    }

    /**
     * Build complete timeline for replay
     */
    private function buildRecordingTimeline(BTLiveRecording $recording): void
    {
        $session = $recording->session;
        $timeline = [];

        // Get all events from replay timeline
        $events = BTLiveReplayTimeline::where('session_id', $session->id)
            ->orderBy('timestamp')
            ->get();

        foreach ($events as $event) {
            $timeline[] = [
                'time' => $event->timestamp,
                'type' => $event->event_type,
                'data' => $event->data,
                'ref' => $event->reference_id,
            ];
        }

        $recording->update(['timeline' => $timeline]);
    }

    /**
     * Export session data for replay
     */
    private function exportSessionData(BTLiveRecording $recording): void
    {
        $session = $recording->session;

        // PDF sequence
        $pdfSequence = $session->pdfs()
            ->ordered()
            ->get()
            ->map(fn($pdf) => [
                'id' => $pdf->id,
                'title' => $pdf->title,
                'pages' => $pdf->total_pages,
                'url' => $pdf->file_url,
            ])
            ->toArray();

        // Chat export (optional - for moderation review)
        $chatExport = $session->chatMessages()
            ->visible()
            ->with('participant')
            ->orderBy('timestamp')
            ->get()
            ->map(fn($msg) => [
                'time' => $msg->timestamp,
                'name' => $msg->participant?->name ?? 'System',
                'type' => $msg->message_type,
                'content' => $msg->content,
            ])
            ->toArray();

        // Poll results
        $pollResults = $session->polls()
            ->with('answers')
            ->get()
            ->map(fn($poll) => [
                'id' => $poll->id,
                'question' => $poll->question,
                'results' => $poll->getResults(),
            ])
            ->toArray();

        $recording->update([
            'pdf_sequence' => $pdfSequence,
            'chat_export' => $chatExport,
            'poll_results' => $pollResults,
        ]);
    }

    /**
     * Get replay data for frontend
     */
    public function getReplayData(int $recordingId): array
    {
        $recording = BTLiveRecording::with('session')->findOrFail($recordingId);

        if (!$recording->isCompleted()) {
            throw new \Exception('Recording not available');
        }

        $session = $recording->session;

        return [
            'recording' => [
                'id' => $recording->id,
                'duration' => $recording->duration_seconds,
                'video_url' => $recording->teacher_video_url,
            ],
            'session' => [
                'id' => $session->id,
                'title' => $session->title,
                'teacher' => $session->teacher->name ?? 'Teacher',
            ],
            'pdfs' => $recording->pdf_sequence ?? [],
            'timeline' => $recording->timeline ?? [],
            'chat' => $recording->chat_export ?? [],
            'polls' => $recording->poll_results ?? [],
        ];
    }

    /**
     * Get replay state at specific timestamp
     */
    public function getReplayStateAt(int $recordingId, int $timestamp): array
    {
        $recording = BTLiveRecording::findOrFail($recordingId);
        $timeline = $recording->timeline ?? [];

        $state = [
            'current_time' => $timestamp,
            'video_position' => $timestamp,
            'active_pdf' => null,
            'current_page' => 1,
            'whiteboard_state' => [],
            'active_polls' => [],
            'recent_chat' => [],
        ];

        // Process all events up to this timestamp
        foreach ($timeline as $event) {
            if ($event['time'] > $timestamp) {
                break;
            }

            switch ($event['type']) {
                case 'pdf_open':
                    $state['active_pdf'] = $event['data']['pdf_id'] ?? null;
                    break;

                case 'page_change':
                    $state['current_page'] = $event['data']['page_number'] ?? 1;
                    break;

                case 'annotation_start':
                    // Load whiteboard annotations for this PDF/page
                    break;

                case 'poll_start':
                    $state['active_polls'][] = $event['data']['poll_id'];
                    break;

                case 'poll_end':
                    $state['active_polls'] = array_diff(
                        $state['active_polls'],
                        [$event['data']['poll_id']]
                    );
                    break;

                case 'chat_message':
                    // Add to recent chat (keep last 50)
                    break;
            }
        }

        // Load whiteboard events for current PDF/page
        if ($state['active_pdf']) {
            $state['whiteboard_state'] = $this->getWhiteboardState(
                $recording->session_id,
                $state['active_pdf'],
                $state['current_page'],
                $timestamp
            );
        }

        return $state;
    }

    /**
     * Get whiteboard state at a specific point in time
     */
    private function getWhiteboardState(int $sessionId, int $pdfId, int $page, int $timestamp): array
    {
        return \App\Models\BTLiveWhiteboardEvent::getEventsForReplay($sessionId, $pdfId, $page, 0, $timestamp);
    }

    /**
     * Approve a recording
     */
    public function approveRecording(BTLiveRecording $recording, int $userId, ?string $notes = null): void
    {
        $recording->update([
            'is_approved' => true,
            'approved_by' => $userId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Reject a recording
     */
    public function rejectRecording(BTLiveRecording $recording, int $userId, ?string $notes = null): void
    {
        $recording->update([
            'is_approved' => false,
            'approved_by' => $userId,
            'approved_at' => now(),
            'approval_notes' => $notes,
        ]);
    }

    /**
     * Upload teacher video
     */
    public function uploadTeacherVideo(BTLiveRecording $recording, $file): void
    {
        $path = $file->store('recordings/' . $recording->tenant_id, 'public');

        $recording->update([
            'teacher_video_path' => $path,
            'teacher_video_url' => Storage::url($path),
            'teacher_video_size' => $file->getSize(),
        ]);
    }

    /**
     * Get all recordings for a tenant
     */
    public function getRecordingsForTenant(int $tenantId, array $filters = []): array
    {
        $query = BTLiveRecording::where('tenant_id', $tenantId)
            ->with('session');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_approved'])) {
            $query->where('is_approved', $filters['is_approved']);
        }

        if (isset($filters['search'])) {
            $query->whereHas('session', function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderByDesc('created_at')->get()->toArray();
    }

    /**
     * Calculate recording duration
     */
    private function calculateDuration(BTLiveRecording $recording): int
    {
        if (!$recording->started_at || !$recording->ended_at) {
            return 0;
        }
        return $recording->started_at->diffInSeconds($recording->ended_at);
    }
}
