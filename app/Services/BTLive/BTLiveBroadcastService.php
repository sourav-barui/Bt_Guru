<?php

namespace App\Services\BTLive;

use App\Models\BTLiveSession;
use App\Models\BTLivePdf;
use App\Models\BTLiveWhiteboardEvent;
use App\Models\BTLivePoll;
use App\Models\BTLiveChatMessage;
use App\Models\BTLiveRaisedHand;
use App\Models\BTLiveReplayTimeline;

/**
 * Service for managing broadcast events from teacher to students
 */
class BTLiveBroadcastService
{
    /**
     * Broadcast PDF page change
     */
    public function broadcastPdfPageChange(BTLiveSession $session, int $pdfId, int $pageNumber): array
    {
        $timestamp = $this->getTimestamp($session);

        // Update PDF current page
        BTLivePdf::where('id', $pdfId)->update(['current_page' => $pageNumber]);

        // Update session
        $session->update([
            'current_pdf_id' => $pdfId,
            'current_pdf_page' => $pageNumber,
        ]);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $session->id,
            'page_change',
            [
                'pdf_id' => $pdfId,
                'page_number' => $pageNumber,
            ],
            $timestamp
        );

        return [
            'type' => 'pdf_page_change',
            'pdf_id' => $pdfId,
            'page_number' => $pageNumber,
            'timestamp' => $timestamp,
        ];
    }

    /**
     * Broadcast whiteboard annotation
     */
    public function broadcastAnnotation(
        BTLiveSession $session,
        string $tool,
        array $toolConfig,
        array $data,
        ?int $pdfId = null,
        int $pageNumber = 1,
        int $teacherId
    ): array {
        $timestamp = $this->getTimestamp($session);

        // Store whiteboard event
        $event = BTLiveWhiteboardEvent::create([
            'session_id' => $session->id,
            'pdf_id' => $pdfId,
            'page_number' => $pageNumber,
            'event_type' => 'draw',
            'tool' => $tool,
            'tool_config' => $toolConfig,
            'data' => $data,
            'created_by' => $teacherId,
            'timestamp' => $timestamp,
            'is_synced' => true,
        ]);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $session->id,
            'annotation_start',
            [
                'event_id' => $event->id,
                'tool' => $tool,
                'pdf_id' => $pdfId,
                'page' => $pageNumber,
            ],
            $timestamp,
            $event->id
        );

        return [
            'type' => 'annotation',
            'event_id' => $event->id,
            'tool' => $tool,
            'config' => $toolConfig,
            'data' => $data,
            'pdf_id' => $pdfId,
            'page_number' => $pageNumber,
            'timestamp' => $timestamp,
        ];
    }

    /**
     * Clear whiteboard
     */
    public function clearWhiteboard(BTLiveSession $session, ?int $pdfId = null, int $pageNumber = 1, int $teacherId): array
    {
        $timestamp = $this->getTimestamp($session);

        // Create clear event
        BTLiveWhiteboardEvent::create([
            'session_id' => $session->id,
            'pdf_id' => $pdfId,
            'page_number' => $pageNumber,
            'event_type' => 'clear',
            'tool' => 'eraser',
            'tool_config' => [],
            'data' => ['cleared' => true],
            'created_by' => $teacherId,
            'timestamp' => $timestamp,
            'is_synced' => true,
        ]);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $session->id,
            'whiteboard_clear',
            [
                'pdf_id' => $pdfId,
                'page' => $pageNumber,
            ],
            $timestamp
        );

        return [
            'type' => 'whiteboard_clear',
            'pdf_id' => $pdfId,
            'page_number' => $pageNumber,
            'timestamp' => $timestamp,
        ];
    }

    /**
     * Start a poll
     */
    public function startPoll(
        BTLiveSession $session,
        string $question,
        array $options,
        int $teacherId,
        ?int $correctOptionIndex = null,
        int $durationSeconds = 60,
        bool $isAnonymous = true
    ): BTLivePoll {
        $timestamp = $this->getTimestamp($session);

        $poll = BTLivePoll::create([
            'session_id' => $session->id,
            'created_by' => $teacherId,
            'question' => $question,
            'options' => $options,
            'correct_option_index' => $correctOptionIndex,
            'is_multiple_choice' => false,
            'is_anonymous' => $isAnonymous,
            'status' => 'active',
            'started_at' => now(),
            'duration_seconds' => $durationSeconds,
            'show_results_to_students' => true,
            'timestamp' => $timestamp,
        ]);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $session->id,
            'poll_start',
            [
                'poll_id' => $poll->id,
                'question' => $question,
                'options_count' => count($options),
            ],
            $timestamp,
            $poll->id
        );

        // Auto-close after duration
        if ($durationSeconds > 0) {
            // TODO: Dispatch job to close poll after duration
        }

        return $poll;
    }

    /**
     * Close/end a poll
     */
    public function closePoll(BTLivePoll $poll): array
    {
        $timestamp = $this->getTimestamp($poll->session);

        $poll->close();

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $poll->session_id,
            'poll_end',
            [
                'poll_id' => $poll->id,
                'total_votes' => $poll->answers()->count(),
            ],
            $timestamp,
            $poll->id
        );

        return [
            'type' => 'poll_closed',
            'poll_id' => $poll->id,
            'results' => $poll->getResults(),
            'timestamp' => $timestamp,
        ];
    }

    /**
     * Reveal poll results to students
     */
    public function revealPollResults(BTLivePoll $poll): array
    {
        $timestamp = $this->getTimestamp($poll->session);

        $poll->revealResults();

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $poll->session_id,
            'poll_reveal',
            [
                'poll_id' => $poll->id,
                'results' => $poll->getResults(),
            ],
            $timestamp,
            $poll->id
        );

        return [
            'type' => 'poll_results_revealed',
            'poll_id' => $poll->id,
            'results' => $poll->getResults(),
            'timestamp' => $timestamp,
        ];
    }

    /**
     * Submit poll answer
     */
    public function submitPollAnswer(BTLivePoll $poll, int $participantId, int $optionIndex): array
    {
        $timestamp = $this->getTimestamp($poll->session);

        $answer = \App\Models\BTLivePollAnswer::create([
            'poll_id' => $poll->id,
            'participant_id' => $participantId,
            'option_index' => $optionIndex,
            'answered_at' => now(),
            'timestamp' => $timestamp,
        ]);

        return [
            'type' => 'poll_answer_submitted',
            'poll_id' => $poll->id,
            'participant_id' => $participantId,
            'option_index' => $optionIndex,
            'timestamp' => $timestamp,
        ];
    }

    /**
     * Send chat message
     */
    public function sendChatMessage(
        BTLiveSession $session,
        string $content,
        string $messageType = 'text',
        ?int $participantId = null,
        ?int $replyToId = null
    ): BTLiveChatMessage {
        $timestamp = $this->getTimestamp($session);

        $message = BTLiveChatMessage::create([
            'session_id' => $session->id,
            'participant_id' => $participantId,
            'message_type' => $messageType,
            'content' => $content,
            'reply_to_id' => $replyToId,
            'timestamp' => $timestamp,
        ]);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $session->id,
            'chat_message',
            [
                'message_id' => $message->id,
                'type' => $messageType,
                'participant_id' => $participantId,
                'content_preview' => substr($content, 0, 50),
            ],
            $timestamp,
            $message->id
        );

        return $message;
    }

    /**
     * Raise hand
     */
    public function raiseHand(BTLiveSession $session, int $participantId, ?string $reason = null): BTLiveRaisedHand
    {
        $timestamp = $this->getTimestamp($session);

        $raisedHand = BTLiveRaisedHand::create([
            'session_id' => $session->id,
            'participant_id' => $participantId,
            'status' => 'raised',
            'raised_at' => now(),
            'reason' => $reason,
            'timestamp' => $timestamp,
        ]);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $session->id,
            'hand_raised',
            [
                'hand_id' => $raisedHand->id,
                'participant_id' => $participantId,
                'reason' => $reason,
            ],
            $timestamp,
            $raisedHand->id
        );

        return $raisedHand;
    }

    /**
     * Accept raised hand
     */
    public function acceptHand(BTLiveRaisedHand $raisedHand, int $teacherId): void
    {
        $timestamp = $this->getTimestamp($raisedHand->session);

        $raisedHand->accept($teacherId);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $raisedHand->session_id,
            'hand_accepted',
            [
                'hand_id' => $raisedHand->id,
                'participant_id' => $raisedHand->participant_id,
                'teacher_id' => $teacherId,
            ],
            $timestamp,
            $raisedHand->id
        );
    }

    /**
     * Reject raised hand
     */
    public function rejectHand(BTLiveRaisedHand $raisedHand, int $teacherId): void
    {
        $timestamp = $this->getTimestamp($raisedHand->session);

        $raisedHand->reject($teacherId);

        // Add timeline event
        BTLiveReplayTimeline::addEvent(
            $raisedHand->session_id,
            'hand_rejected',
            [
                'hand_id' => $raisedHand->id,
                'participant_id' => $raisedHand->participant_id,
                'teacher_id' => $teacherId,
            ],
            $timestamp,
            $raisedHand->id
        );
    }

    /**
     * Mute/unmute all students
     */
    public function muteAllStudents(BTLiveSession $session, bool $mute = true): int
    {
        $count = $session->participants()
            ->where('role', 'student')
            ->where('is_active', true)
            ->update(['is_muted' => $mute]);

        return $count;
    }

    /**
     * Block/unblock all student screens
     */
    public function blockAllStudents(BTLiveSession $session, bool $block = true): int
    {
        $count = $session->participants()
            ->where('role', 'student')
            ->where('is_active', true)
            ->update(['is_screen_blocked' => $block]);

        return $count;
    }

    /**
     * Get current state for new participant
     */
    public function getCurrentState(BTLiveSession $session): array
    {
        return [
            'session' => [
                'id' => $session->id,
                'status' => $session->status,
                'title' => $session->title,
                'current_pdf_id' => $session->current_pdf_id,
                'current_pdf_page' => $session->current_pdf_page,
                'features' => [
                    'chat_enabled' => $session->chat_enabled,
                    'raise_hand_enabled' => $session->raise_hand_enabled,
                    'polls_enabled' => $session->polls_enabled,
                    'whiteboard_enabled' => $session->whiteboard_enabled,
                ],
            ],
            'active_pdf' => $session->current_pdf_id ? $session->pdfs()->find($session->current_pdf_id) : null,
            'active_polls' => $session->polls()->active()->get(),
            'recent_chat' => $session->chatMessages()
                ->visible()
                ->orderByDesc('timestamp')
                ->limit(50)
                ->get()
                ->reverse()
                ->values(),
            'raised_hands' => $session->raisedHands()->active()->ordered()->get(),
            'participant_count' => $session->participant_count,
        ];
    }

    /**
     * Get timestamp in ms from session start
     */
    private function getTimestamp(BTLiveSession $session): int
    {
        if (!$session->started_at) {
            return 0;
        }
        return now()->diffInMilliseconds($session->started_at);
    }
}
