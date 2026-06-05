<?php

namespace App\Services;

use App\Models\LiveClass;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class BTLiveService
{
    // Jitsi configuration
    protected string $jitsiDomain;
    protected ?string $appId;
    protected ?string $appSecret;
    
    // Scalability settings
    protected array $scalabilityConfig = [
        'lastN' => 1,              // Only show teacher video
        'simulcast' => true,
        'adaptiveBitrate' => true,
        'enableVideoSuspension' => true,
        'channelLastN' => -1,
        'startVideoMuted' => 500,  // Mute video after 500 participants
        'startAudioMuted' => 500,  // Mute audio after 500 participants
    ];
    
    public function __construct()
    {
        $this->jitsiDomain = config('btlive.jitsi_domain', 'meet.jit.si');
        $this->appId = config('btlive.jitsi_app_id');
        $this->appSecret = config('btlive.jitsi_app_secret');
    }
    
    /**
     * Generate a unique BTLive room name
     */
    public function generateRoomName(LiveClass $liveClass): string
    {
        $prefix = 'btlive';
        $tenantSlug = $liveClass->tenant->slug ?? 'tenant';
        $courseId = $liveClass->course_id;
        $classId = $liveClass->id;
        $random = substr(md5(uniqid()), 0, 8);
        
        return "{$prefix}-{$tenantSlug}-{$courseId}-{$classId}-{$random}";
    }
    
    /**
     * Generate JWT token for moderator (Teacher)
     */
    public function generateModeratorToken(LiveClass $liveClass, User $teacher): string
    {
        if (!$this->appSecret) {
            // Return empty if JWT not configured (dev mode)
            return '';
        }
        
        $payload = [
            'iss' => $this->appId ?? 'bt_guru',
            'aud' => 'jitsi',
            'sub' => $this->jitsiDomain,
            'iat' => time(),
            'exp' => time() + (4 * 3600), // 4 hours
            'room' => $liveClass->btlive_room_name,
            'moderator' => true,
            'context' => [
                'user' => [
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'avatar' => $teacher->avatar_url ?? null,
                    'id' => (string) $teacher->id,
                ],
                'group' => 'teachers',
            ],
            'features' => [
                'livestreaming' => true,
                'recording' => true,
                'transcription' => false,
                'outbound-call' => false,
            ],
        ];
        
        return $this->encodeJwt($payload);
    }
    
    /**
     * Generate JWT token for student
     */
    public function generateStudentToken(LiveClass $liveClass, User $student): string
    {
        if (!$this->appSecret) {
            return '';
        }
        
        $payload = [
            'iss' => $this->appId ?? 'bt_guru',
            'aud' => 'jitsi',
            'sub' => $this->jitsiDomain,
            'iat' => time(),
            'exp' => time() + (4 * 3600),
            'room' => $liveClass->btlive_room_name,
            'moderator' => false,
            'context' => [
                'user' => [
                    'name' => $student->name,
                    'email' => $student->email,
                    'avatar' => $student->avatar_url ?? null,
                    'id' => (string) $student->id,
                ],
                'group' => 'students',
            ],
            'features' => [
                'livestreaming' => false,
                'recording' => false,
                'transcription' => false,
            ],
        ];
        
        return $this->encodeJwt($payload);
    }
    
    /**
     * Encode JWT token
     */
    protected function encodeJwt(array $payload): string
    {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);
        $payload = json_encode($payload);
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $this->appSecret, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64Header . '.' . $base64Payload . '.' . $base64Signature;
    }
    
    /**
     * Get Jitsi meeting URL
     */
    public function getMeetingUrl(LiveClass $liveClass, string $jwt = ''): string
    {
        $url = "https://{$this->jitsiDomain}/{$liveClass->btlive_room_name}";
        
        if ($jwt) {
            $url .= '?jwt=' . $jwt;
        }
        
        return $url;
    }
    
    /**
     * Get Jitsi SDK configuration for embedded iframe
     */
    public function getJitsiConfig(LiveClass $liveClass, User $user, bool $isModerator = false): array
    {
        $config = [
            'domain' => $this->jitsiDomain,
            'roomName' => $liveClass->btlive_room_name,
            'width' => '100%',
            'height' => '100%',
            'parentNode' => null, // Will be set in JS
            'configOverwrite' => [
                // Scalability settings
                'lastNLimits' => [
                    '5' => 1,
                    '30' => 1,
                    '50' => 1,
                    '75' => 1,
                    '100' => 1,
                    '200' => 1,
                    '300' => 1,
                    '400' => 1,
                    '500' => 1,
                ],
                'startLastN' => 1,
                'channelLastN' => 1,
                
                // Video quality
                'resolution' => 360,
                'constraints' => [
                    'video' => [
                        'height' => ['ideal' => 360, 'max' => 360],
                        'frameRate' => ['max' => 15],
                    ],
                ],
                
                // Audio/video - Teacher only, students disabled
                'startWithAudioMuted' => !$isModerator,
                'startWithVideoMuted' => !$isModerator,
                'disableAudioLevels' => !$isModerator,
                'disableLocalVideoFlip' => !$isModerator,
                
                // Students: audio/video completely disabled
                'disableVideo' => !$isModerator,
                'disableAudio' => !$isModerator,
                
                // Disable features for students
                'disableModeratorIndicator' => !$isModerator,
                'hideModeratorSettingsTab' => !$isModerator,
                
                // Security
                'enableLobby' => $liveClass->btlive_lobby_enabled && !$isModerator,
                'enableWaitingRoom' => $liveClass->btlive_waiting_room_enabled && !$isModerator,
                
                // Recording (teacher only)
                'liveStreamingEnabled' => $isModerator,
                'fileRecordingsEnabled' => $isModerator,
                
                // Chat
                'disableChat' => !$liveClass->btlive_chat_enabled,
                
                // UI customization - Disable all prompts for auto-join
                'defaultLanguage' => 'en',
                'prejoinPageEnabled' => false,
                'enableWelcomePage' => false,
                'enableClosePage' => false,
                'disableDeepLinking' => true,
                'disableInviteFunctions' => true,
                'hideConferenceSubject' => true,
                'hideConferenceTimer' => true,
                'hideParticipantsStats' => true,
                
                // Bandwidth optimization
                'disableSimulcast' => false,
                'enableAdaptiveMode' => true,
            ],
            'interfaceConfigOverwrite' => [
                'APP_NAME' => 'BTLive',
                'NATIVE_APP_NAME' => 'BTLive',
                'PROVIDER_NAME' => 'BT Guru',
                'SHOW_JITSI_WATERMARK' => false,
                'SHOW_WATERMARK_FOR_GUESTS' => false,
                'SHOW_POWERED_BY' => false,
                'JITSI_WATERMARK_LINK' => '',
                'DEFAULT_BACKGROUND' => '#f3f4f6',
                'DISABLE_TRANSCRIPTION_SUBTITLES' => true,
                'DISABLE_VIDEO_BACKGROUND' => true,
                'HIDE_INVITE_MORE_HEADER' => true,
                'MOBILE_APP_PROMO' => false,
                'ENABLE_FEEDBACK_ANIMATION' => false,
                'DISABLE_FOCUS_INDICATOR' => true,
                'DISABLE_DOMINANT_SPEAKER_INDICATOR' => false,
                
                // Hide features based on role
                'TOOLBAR_BUTTONS' => $isModerator 
                    ? [
                        'microphone', 'camera', 'desktop', 'fullscreen',
                        'fodeviceselection', 'hangup', 'profile', 'chat',
                        'recording', 'livestreaming', 'settings', 'videoquality',
                        'tileview', 'download', 'help', 'mute-everyone',
                        'security', 'raisehand', 'participants-pane'
                    ]
                    : [
                        // Students: chat only (no mic, no camera)
                        'fullscreen', 'hangup', 'profile', 'chat', 
                        'raisehand', 'participants-pane'
                    ],
            ],
            'userInfo' => [
                'displayName' => $user->name,
                'email' => $user->email,
            ],
        ];
        
        return $config;
    }
    
    /**
     * Start BTLive session
     */
    public function startSession(LiveClass $liveClass): void
    {
        if (!$liveClass->is_btlive) {
            throw new \InvalidArgumentException('Not a BTLive class');
        }
        
        $liveClass->update([
            'btlive_started_at' => now(),
            'status' => 'live',
            'btlive_recording_status' => 'pending',
        ]);
    }
    
    /**
     * End BTLive session
     */
    public function endSession(LiveClass $liveClass): void
    {
        if (!$liveClass->is_btlive) {
            return;
        }
        
        $liveClass->update([
            'btlive_ended_at' => now(),
            'status' => 'completed',
        ]);
        
        // Calculate final attendance
        $this->finalizeAttendance($liveClass);
    }
    
    /**
     * Finalize attendance records when class ends
     */
    protected function finalizeAttendance(LiveClass $liveClass): void
    {
        $attendanceRecords = $liveClass->attendance()->whereNull('left_at')->get();
        
        foreach ($attendanceRecords as $record) {
            $record->update([
                'left_at' => now(),
                'duration_seconds' => now()->diffInSeconds($record->joined_at),
            ]);
        }
    }
    
    /**
     * Handle recording webhook from Jitsi
     */
    public function handleRecordingWebhook(LiveClass $liveClass, array $data): void
    {
        Log::info('BTLive recording webhook', ['live_class_id' => $liveClass->id, 'data' => $data]);
        
        if (isset($data['status'])) {
            $liveClass->update([
                'btlive_recording_status' => $data['status'],
            ]);
        }
        
        if (isset($data['url']) && $data['status'] === 'available') {
            $liveClass->update([
                'btlive_recording_url' => $data['url'],
                'video_url' => $data['url'], // Backward compatibility
                'btlive_recording_status' => 'completed',
            ]);
        }
        
        if (isset($data['recording_id'])) {
            $liveClass->update(['btlive_recording_id' => $data['recording_id']]);
        }
    }
    
    /**
     * Kick participant from room
     */
    public function kickParticipant(LiveClass $liveClass, string $participantId, string $reason = ''): bool
    {
        // In real implementation, this would call Jitsi API
        // For now, we just log and update local attendance record
        Log::info('BTLive kick participant', [
            'live_class_id' => $liveClass->id,
            'participant_id' => $participantId,
            'reason' => $reason,
        ]);
        
        // Update attendance record if exists
        $attendance = $liveClass->attendance()
            ->where('jitsi_participant_id', $participantId)
            ->whereNull('left_at')
            ->first();
            
        if ($attendance) {
            $attendance->update([
                'left_at' => now(),
                'was_kicked' => true,
                'kick_reason' => $reason,
                'duration_seconds' => now()->diffInSeconds($attendance->joined_at),
            ]);
        }
        
        return true;
    }
    
    /**
     * Get attendance statistics for a class
     */
    public function getAttendanceStats(LiveClass $liveClass): array
    {
        $attendance = $liveClass->attendance();
        
        $totalStudents = $attendance->count();
        $presentStudents = $attendance->whereNotNull('joined_at')->count();
        $averageDuration = $attendance->avg('duration_seconds') ?? 0;
        
        // Calculate attendance percentage
        $enrolledCount = $liveClass->course->enrollments()
            ->where('enrollment_status', 'active')
            ->count();
            
        $attendancePercentage = $enrolledCount > 0 
            ? round(($presentStudents / $enrolledCount) * 100, 2)
            : 0;
        
        return [
            'total_enrolled' => $enrolledCount,
            'total_present' => $presentStudents,
            'attendance_percentage' => $attendancePercentage,
            'average_duration_minutes' => round($averageDuration / 60, 2),
            'total_duration_seconds' => (int) $averageDuration,
        ];
    }
}
