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
    
    // Recording Storage Configuration
    'recordings' => [
        'enabled' => env('BTLIVE_RECORDING_ENABLED', true),
        'local_enabled' => env('BTLIVE_LOCAL_STORAGE', true),
        'local_path' => env('BTLIVE_RECORDINGS_PATH', '/var/lib/jitsi-meet/recordings'),
        
        // S3 Configuration
        's3_enabled' => env('BTLIVE_S3_ENABLED', false),
        's3_disk' => env('BTLIVE_S3_DISK', 's3'), // Laravel disk name
        's3_bucket' => env('BTLIVE_S3_BUCKET', null),
        's3_region' => env('BTLIVE_S3_REGION', 'us-east-1'),
        's3_access_key' => env('BTLIVE_S3_ACCESS_KEY', null),
        's3_secret_key' => env('BTLIVE_S3_SECRET_KEY', null),
        's3_endpoint' => env('BTLIVE_S3_ENDPOINT', null), // For MinIO/DigitalOcean Spaces
        
        // Recording Limits
        'max_duration' => env('BTLIVE_MAX_RECORDING_DURATION', 240), // minutes
        'auto_cleanup_days' => env('BTLIVE_AUTO_CLEANUP_DAYS', 30), // delete after X days
        'max_storage_per_tenant_gb' => env('BTLIVE_MAX_STORAGE_GB', 50),
    ],
    
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
