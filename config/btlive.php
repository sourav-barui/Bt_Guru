<?php

return [
    /*
    |--------------------------------------------------------------------------
    | BTLive Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the BTLive native classroom system powered by Jitsi.
    |
    */

    // Jitsi Meet domain (use your own if self-hosted)
    'jitsi_domain' => env('BTLIVE_JITSI_DOMAIN', 'meet.jit.si'),
    
    // JWT Authentication (required for moderator controls)
    'jitsi_app_id' => env('BTLIVE_JITSI_APP_ID', null),
    'jitsi_app_secret' => env('BTLIVE_JITSI_APP_SECRET', null),
    
    // Webhook secret for recording callbacks
    'webhook_secret' => env('BTLIVE_WEBHOOK_SECRET', null),
    
    // Scalability settings
    'max_participants' => env('BTLIVE_MAX_PARTICIPANTS', 5000),
    'max_teachers_live' => env('BTLIVE_MAX_TEACHERS', 20),
    
    // Video quality settings
    'teacher_video_resolution' => env('BTLIVE_TEACHER_RESOLUTION', 360),
    'teacher_video_fps' => env('BTLIVE_TEACHER_FPS', 15),
    'enable_simulcast' => env('BTLIVE_SIMULCAST', true),
    
    // Recording settings
    'auto_start_recording' => env('BTLIVE_AUTO_RECORD', false),
    'recording_format' => env('BTLIVE_RECORD_FORMAT', 'mp4'),
    
    // Security settings
    'require_jwt' => env('BTLIVE_REQUIRE_JWT', true),
    'enable_lobby' => env('BTLIVE_ENABLE_LOBBY', true),
    'enable_waiting_room' => env('BTLIVE_ENABLE_WAITING_ROOM', true),
    
    // Feature toggles
    'features' => [
        'chat' => true,
        'raise_hand' => true,
        'screen_share_teacher_only' => true,
        'recording' => true,
        'livestreaming' => false,
        'transcription' => false,
    ],
    
    // Attendance settings
    'attendance' => [
        'track_device' => true,
        'track_ip' => true,
        'min_duration_for_credit' => 300, // 5 minutes
        'auto_mark_present_threshold' => 0.5, // 50% of class duration
    ],
];
