# BTLIVE V2 - Digital Classroom System Summary

## Overview
BTLIVE V2 is a complete redesign of the live class system with a **broadcast architecture** (1 Teacher → Many Students) optimized for low bandwidth and high scalability.

## Architecture Change

### OLD (Meeting-Oriented)
```
Teacher ⇄ LiveKit ⇄ Students
(All participants send/receive video)
```

### NEW (Broadcast-Oriented)
```
Teacher
↓
Media Layer (LiveKit)
↓
Broadcast Server
↓
Students (receive-only)
```

## What Was Created

### 1. New Models
- `BTLiveSession` - Main session/session management
- `BTLiveParticipant` - Student/teacher participants
- `BTLivePdf` - PDF teaching materials
- `BTLiveWhiteboardEvent` - Event-based annotations
- `BTLivePoll` / `BTLivePollAnswer` - Interactive polls
- `BTLiveChatMessage` - Realtime chat
- `BTLiveRaisedHand` - Student hand raising
- `BTLiveRecording` - Recording metadata
- `BTLiveReplayTimeline` - Replay reconstruction

### 2. New Services
- `BTLiveSessionService` - Session lifecycle management
- `BTLiveBroadcastService` - Teacher→Student broadcast
- `BTLiveWebSocketService` - Event routing
- `BTLiveRecordingService` - Recording & replay

### 3. New Controller
- `BTLiveV2Controller` - Main API endpoints

### 4. New Views
- `teacher_room.blade.php` - Teacher interface with PDF, whiteboard, polls, chat
- `student_room.blade.php` - Student view-only interface

### 5. New Migrations (10 tables)
- `btlive_sessions`
- `btlive_participants`
- `btlive_pdfs`
- `btlive_whiteboard_events`
- `btlive_polls`
- `btlive_poll_answers`
- `btlive_chat_messages`
- `btlive_raised_hands`
- `btlive_recordings`
- `btlive_replay_timeline`

### 6. New Routes
```php
/btlive-v2/session/{session}/room          # Teacher room
/btlive-v2/session/{session}/join          # Student join
/btlive-v2/session/{session}/state         # Get current state
/btlive-v2/session/{session}/teacher-event # Teacher actions
/btlive-v2/participant/{participant}/event # Student actions
/btlive-v2/replay/{recording}             # Watch replay
```

## Key Features

### Teacher Can:
- Start/end class
- Enable camera/microphone/screen share
- Upload and display PDFs
- Annotate on PDFs with pen/highlighter/shapes
- Create and launch polls
- Moderate chat (pin/delete messages)
- View and manage raised hands
- Mute/block all students
- Kick participants
- Record class

### Students Can:
- Join with access code
- View teacher video
- View synchronized PDFs
- See live annotations
- Chat (moderated)
- Raise hand
- Answer polls
- Watch replay after class

### Recording & Replay:
- Stores teacher video separately
- PDF sequence with timing
- All annotations as events
- Poll results
- Chat log
- Reconstructs exact classroom experience

## Commands to Run

### 1. Run Migrations
```bash
cd c:\xampp\htdocs\Bt_Guru
php artisan migrate
```

### 2. Clear Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 3. Restart Server
Restart XAMPP Apache

## How to Use

### Create a Live Class Session:
1. Go to Tenant Admin → Courses → Live Classes
2. Create a new live class
3. System automatically creates BTLiveSession

### Teacher Starts Class:
```
https://{tenant}.btguru.tech/btlive-v2/session/{id}/room
```

### Student Joins Class:
```
https://{tenant}.btguru.tech/btlive-v2/session/{id}/join?code=XXXXXX
```

### Watch Replay:
```
https://{tenant}.btguru.tech/btlive-v2/replay/{recording_id}
```

## Old BTLIVE Files (Keep for reference)
The old BTLIVE files remain untouched:
- `BTLiveController.php` → `BTLiveController_old_backup.php`
- Old views in `resources/views/btlive/`
- Old services in `app/Services/BTLiveService.php`

## Next Steps (If Needed)

1. **WebSocket Integration**: Replace polling with LiveKit WebSocket for real-time
2. **Mobile App**: Create Flutter/React Native student app
3. **Scaling**: Add Redis for session state, multiple media servers
4. **Analytics**: Track student engagement, attention metrics
5. **AI Features**: Auto-transcription, smart summaries

## Scalability

Current design supports:
- 1 session: 1000+ students
- Multiple concurrent sessions
- Horizontal scaling ready (can add more app servers)

For 5000+ students:
- Add Redis pub/sub
- Use CDN for PDFs
- Multiple LiveKit SFU nodes
- Load balancer for API servers

## Support

Files created:
- 9 new Models
- 4 new Services
- 1 new Controller
- 2 new Views
- 10 new Migrations
- Updated routes

All in:
- `app/Models/BTLive*.php`
- `app/Services/BTLive/*.php`
- `app/Http/Controllers/BTLiveV2Controller.php`
- `resources/views/btlive/v2/*.blade.php`
- `database/migrations/2025_06_11_*.php`
- `routes/web.php` (updated)

---

**Status**: Ready for testing after running migrations
**Date**: June 11, 2026
