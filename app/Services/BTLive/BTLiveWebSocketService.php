<?php

namespace App\Services\BTLive;

use App\Models\BTLiveSession;
use App\Models\BTLiveParticipant;
use App\Models\BTLiveWhiteboardEvent;

/**
 * Service for WebSocket event handling
 * This integrates with LiveKit for real-time communication
 */
class BTLiveWebSocketService
{
    private BTLiveBroadcastService $broadcastService;
    private BTLiveSessionService $sessionService;

    public function __construct(
        BTLiveBroadcastService $broadcastService,
        BTLiveSessionService $sessionService
    ) {
        $this->broadcastService = $broadcastService;
        $this->sessionService = $sessionService;
    }

    /**
     * Handle teacher events
     */
    public function handleTeacherEvent(string $eventType, array $data, BTLiveSession $session, int $teacherId): ?array
    {
        return match ($eventType) {
            'session_start' => $this->handleSessionStart($session, $teacherId),
            'session_end' => $this->handleSessionEnd($session, $teacherId),
            'pdf_page_change' => $this->handlePdfPageChange($session, $data),
            'annotation' => $this->handleAnnotation($session, $data, $teacherId),
            'whiteboard_clear' => $this->handleWhiteboardClear($session, $data, $teacherId),
            'poll_start' => $this->handlePollStart($session, $data, $teacherId),
            'poll_close' => $this->handlePollClose($session, $data),
            'poll_reveal' => $this->handlePollReveal($session, $data),
            'chat_message' => $this->handleTeacherChat($session, $data),
            'mute_all' => $this->handleMuteAll($session),
            'kick_participant' => $this->handleKickParticipant($session, $data),
            'accept_hand' => $this->handleAcceptHand($session, $data),
            'reject_hand' => $this->handleRejectHand($session, $data),
            'lower_all_hands' => $this->handleLowerAllHands($session),
            default => null,
        };
    }

    /**
     * Handle student events
     */
    public function handleStudentEvent(string $eventType, array $data, BTLiveParticipant $participant): ?array
    {
        $session = $participant->session;

        return match ($eventType) {
            'join' => $this->handleStudentJoin($participant),
            'leave' => $this->handleStudentLeave($participant),
            'chat_message' => $this->handleStudentChat($session, $data, $participant),
            'poll_answer' => $this->handlePollAnswer($session, $data, $participant),
            'raise_hand' => $this->handleRaiseHand($session, $data, $participant),
            'lower_hand' => $this->handleLowerHand($participant),
            'ping' => $this->handlePing($participant),
            default => null,
        };
    }

    /**
     * Handle session start
     */
    private function handleSessionStart(BTLiveSession $session, int $teacherId): array
    {
        $session = $this->sessionService->startSession($session);

        return [
            'type' => 'session_started',
            'session' => $session->toArray(),
            'broadcast' => [
                'type' => 'session_start',
                'message' => 'Class has started',
            ],
        ];
    }

    /**
     * Handle session end
     */
    private function handleSessionEnd(BTLiveSession $session, int $teacherId): array
    {
        $session = $this->sessionService->endSession($session);

        return [
            'type' => 'session_ended',
            'session' => $session->toArray(),
            'broadcast' => [
                'type' => 'session_end',
                'message' => 'Class has ended',
            ],
        ];
    }

    /**
     * Handle PDF page change
     */
    private function handlePdfPageChange(BTLiveSession $session, array $data): array
    {
        return $this->broadcastService->broadcastPdfPageChange(
            $session,
            $data['pdf_id'],
            $data['page_number']
        );
    }

    /**
     * Handle annotation
     */
    private function handleAnnotation(BTLiveSession $session, array $data, int $teacherId): array
    {
        return $this->broadcastService->broadcastAnnotation(
            $session,
            $data['tool'],
            $data['tool_config'] ?? [],
            $data['data'],
            $data['pdf_id'] ?? null,
            $data['page_number'] ?? 1,
            $teacherId
        );
    }

    /**
     * Handle whiteboard clear
     */
    private function handleWhiteboardClear(BTLiveSession $session, array $data, int $teacherId): array
    {
        return $this->broadcastService->clearWhiteboard(
            $session,
            $data['pdf_id'] ?? null,
            $data['page_number'] ?? 1,
            $teacherId
        );
    }

    /**
     * Handle poll start
     */
    private function handlePollStart(BTLiveSession $session, array $data, int $teacherId): array
    {
        $poll = $this->broadcastService->startPoll(
            $session,
            $data['question'],
            $data['options'],
            $teacherId,
            $data['correct_option'] ?? null,
            $data['duration'] ?? 60,
            $data['is_anonymous'] ?? true
        );

        return [
            'type' => 'poll_started',
            'poll' => $poll->toArray(),
            'broadcast' => [
                'type' => 'poll_start',
                'poll' => [
                    'id' => $poll->id,
                    'question' => $poll->question,
                    'options' => $poll->options,
                ],
            ],
        ];
    }

    /**
     * Handle poll close
     */
    private function handlePollClose(BTLiveSession $session, array $data): array
    {
        $poll = \App\Models\BTLivePoll::find($data['poll_id']);
        if (!$poll) {
            return ['error' => 'Poll not found'];
        }

        return $this->broadcastService->closePoll($poll);
    }

    /**
     * Handle poll reveal
     */
    private function handlePollReveal(BTLiveSession $session, array $data): array
    {
        $poll = \App\Models\BTLivePoll::find($data['poll_id']);
        if (!$poll) {
            return ['error' => 'Poll not found'];
        }

        return $this->broadcastService->revealPollResults($poll);
    }

    /**
     * Handle teacher chat
     */
    private function handleTeacherChat(BTLiveSession $session, array $data): array
    {
        $message = $this->broadcastService->sendChatMessage(
            $session,
            $data['content'],
            'teacher',
            null
        );

        return [
            'type' => 'chat_message_sent',
            'message' => $message->toArray(),
            'broadcast' => [
                'type' => 'chat',
                'message' => [
                    'id' => $message->id,
                    'type' => 'teacher',
                    'content' => $message->content,
                    'timestamp' => $message->timestamp,
                ],
            ],
        ];
    }

    /**
     * Handle mute all
     */
    private function handleMuteAll(BTLiveSession $session): array
    {
        $count = $this->broadcastService->muteAllStudents($session, true);

        return [
            'type' => 'all_muted',
            'count' => $count,
            'broadcast' => [
                'type' => 'mute_all',
                'message' => 'All students have been muted',
            ],
        ];
    }

    /**
     * Handle kick participant
     */
    private function handleKickParticipant(BTLiveSession $session, array $data): array
    {
        $participant = BTLiveParticipant::find($data['participant_id']);
        if ($participant) {
            $this->sessionService->kickParticipant($participant, $data['reason'] ?? null);
        }

        return [
            'type' => 'participant_kicked',
            'participant_id' => $data['participant_id'],
            'broadcast' => [
                'type' => 'participant_removed',
                'participant_id' => $data['participant_id'],
            ],
        ];
    }

    /**
     * Handle accept hand
     */
    private function handleAcceptHand(BTLiveSession $session, array $data): array
    {
        $raisedHand = \App\Models\BTLiveRaisedHand::find($data['hand_id']);
        if ($raisedHand) {
            $this->broadcastService->acceptHand($raisedHand, $data['teacher_id']);
        }

        return [
            'type' => 'hand_accepted',
            'hand_id' => $data['hand_id'],
        ];
    }

    /**
     * Handle reject hand
     */
    private function handleRejectHand(BTLiveSession $session, array $data): array
    {
        $raisedHand = \App\Models\BTLiveRaisedHand::find($data['hand_id']);
        if ($raisedHand) {
            $this->broadcastService->rejectHand($raisedHand, $data['teacher_id']);
        }

        return [
            'type' => 'hand_rejected',
            'hand_id' => $data['hand_id'],
        ];
    }

    /**
     * Handle lower all hands
     */
    private function handleLowerAllHands(BTLiveSession $session): array
    {
        $session->raisedHands()->active()->update([
            'status' => 'lowered',
            'lowered_at' => now(),
        ]);

        return [
            'type' => 'all_hands_lowered',
            'broadcast' => [
                'type' => 'lower_all_hands',
            ],
        ];
    }

    /**
     * Handle student join
     */
    private function handleStudentJoin(BTLiveParticipant $participant): array
    {
        return [
            'type' => 'joined',
            'participant' => $participant->toArray(),
            'state' => $this->broadcastService->getCurrentState($participant->session),
        ];
    }

    /**
     * Handle student leave
     */
    private function handleStudentLeave(BTLiveParticipant $participant): array
    {
        $this->sessionService->removeParticipant($participant);

        return [
            'type' => 'left',
            'participant_id' => $participant->id,
        ];
    }

    /**
     * Handle student chat
     */
    private function handleStudentChat(BTLiveSession $session, array $data, BTLiveParticipant $participant): array
    {
        $message = $this->broadcastService->sendChatMessage(
            $session,
            $data['content'],
            'text',
            $participant->id
        );

        return [
            'type' => 'chat_sent',
            'broadcast' => [
                'type' => 'chat',
                'message' => [
                    'id' => $message->id,
                    'type' => 'text',
                    'participant_id' => $participant->id,
                    'name' => $participant->name,
                    'content' => $message->content,
                    'timestamp' => $message->timestamp,
                ],
            ],
        ];
    }

    /**
     * Handle poll answer
     */
    private function handlePollAnswer(BTLiveSession $session, array $data, BTLiveParticipant $participant): array
    {
        $poll = \App\Models\BTLivePoll::find($data['poll_id']);
        if (!$poll || $poll->status !== 'active') {
            return ['error' => 'Poll not found or not active'];
        }

        // Check if already answered
        if ($poll->hasParticipantAnswered($participant->id)) {
            return ['error' => 'Already answered'];
        }

        $result = $this->broadcastService->submitPollAnswer($poll, $participant->id, $data['option_index']);

        return [
            'type' => 'poll_answered',
            'broadcast' => [
                'type' => 'poll_vote',
                'poll_id' => $poll->id,
                'total_votes' => $poll->answers()->count(),
            ],
        ];
    }

    /**
     * Handle raise hand
     */
    private function handleRaiseHand(BTLiveSession $session, array $data, BTLiveParticipant $participant): array
    {
        $raisedHand = $this->broadcastService->raiseHand(
            $session,
            $participant->id,
            $data['reason'] ?? null
        );

        return [
            'type' => 'hand_raised',
            'hand_id' => $raisedHand->id,
            'broadcast' => [
                'type' => 'hand_raised',
                'hand' => [
                    'id' => $raisedHand->id,
                    'participant_id' => $participant->id,
                    'name' => $participant->name,
                    'reason' => $raisedHand->reason,
                ],
            ],
        ];
    }

    /**
     * Handle lower hand
     */
    private function handleLowerHand(BTLiveParticipant $participant): array
    {
        $participant->raisedHands()
            ->where('status', 'raised')
            ->update([
                'status' => 'lowered',
                'lowered_at' => now(),
            ]);

        return [
            'type' => 'hand_lowered',
            'broadcast' => [
                'type' => 'hand_lowered',
                'participant_id' => $participant->id,
            ],
        ];
    }

    /**
     * Handle ping (keep-alive)
     */
    private function handlePing(BTLiveParticipant $participant): array
    {
        $participant->updateActivity();

        return [
            'type' => 'pong',
            'server_time' => now()->toIso8601String(),
        ];
    }
}
