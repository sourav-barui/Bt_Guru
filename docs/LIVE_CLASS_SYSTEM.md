# BT Guru Live Class System Documentation

> **Purpose:** This document provides comprehensive documentation of the current live class system architecture, database schema, and implementation details. Use this as a reference when building the new Jitsi-based live class system.

---

## Table of Contents

1. [Architecture Overview](#1-architecture-overview)
2. [Database Schema](#2-database-schema)
3. [Models & Relationships](#3-models--relationships)
4. [Admin/Tenant Features](#4-admintenant-features)
5. [Student Features](#5-student-features)
6. [Routes & Controllers](#6-routes--controllers)
7. [Key Implementation Details](#7-key-implementation-details)
8. [Migration Guide for Jitsi](#8-migration-guide-for-jitsi)

---

## 1. Architecture Overview

The current live class system is a **third-party meeting platform aggregator**. It does NOT host its own video infrastructure. Instead, it stores meeting URLs/credentials for external platforms and redirects users to those platforms.

### Supported Platforms
| Platform | Key Field | Password Support |
|----------|-----------|------------------|
| Google Meet | `meeting_url` | ❌ (waiting room) |
| Zoom | `meeting_url` + `meeting_id` | ✅ URL param embedding |
| Microsoft Teams | `meeting_url` | ❌ |
| Jitsi Meet | `meeting_url` | ✅ Hash fragment |
| Other | `meeting_url` | ❌ |

### Core Workflow
1. **Admin creates** live class with meeting URL + scheduled time
2. **System notifies** enrolled students (or all students if public)
3. **Students view** upcoming/live/past classes in their portal
4. **At class time:** Admin clicks "Go Live" → redirects to meeting URL
5. **Students click** "Join" → redirects to same meeting URL
6. **After class:** Admin marks completed, can upload recorded video URL

---

## 2. Database Schema

### Main Table: `live_classes`

```sql
CREATE TABLE live_classes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Tenant & Course (Required)
    tenant_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    
    -- Curriculum Hierarchy (Optional - nullable)
    subject_id BIGINT UNSIGNED NULL,
    chapter_id BIGINT UNSIGNED NULL,
    lesson_id BIGINT UNSIGNED NULL,
    
    -- Class Details
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    
    -- Meeting Platform
    platform ENUM('google_meet', 'zoom', 'ms_teams', 'jitsi', 'other') DEFAULT 'google_meet',
    meeting_url VARCHAR(255) NOT NULL,
    meeting_id VARCHAR(100) NULL,
    meeting_password VARCHAR(100) NULL,
    
    -- Schedule
    scheduled_at DATETIME NOT NULL,
    duration_minutes INT DEFAULT 60,
    
    -- Status
    status ENUM('scheduled', 'live', 'completed', 'cancelled') DEFAULT 'scheduled',
    
    -- Recurrence (for future use)
    recurrence ENUM('none', 'daily', 'weekly') DEFAULT 'none',
    
    -- Public Access
    is_public TINYINT(1) DEFAULT 0,  -- If true, all students can join
    
    -- Recording
    video_url VARCHAR(255) NULL,     -- Link to recorded class
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,        -- Soft deletes
    
    -- Foreign Keys
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
    FOREIGN KEY (chapter_id) REFERENCES chapters(id) ON DELETE SET NULL,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE SET NULL
);
```

### Migration Files (in order)

| File | Purpose |
|------|---------|
| `2025_05_24_000005_create_live_classes_table.php` | Initial table creation |
| `2025_05_24_000006_add_level_ids_to_live_classes_table.php` | Add subject/chapter/lesson FKs |
| `2025_05_28_000003_add_video_url_to_live_classes.php` | Add recorded video field |
| `2025_05_28_000004_add_is_public_to_live_classes.php` | Add public class flag |

---

## 3. Models & Relationships

### LiveClass Model (`app/Models/LiveClass.php`)

```php
// Fillable Fields
protected $fillable = [
    'tenant_id', 'course_id', 'subject_id', 'chapter_id', 'lesson_id',
    'created_by', 'title', 'description', 'platform', 'meeting_url',
    'video_url', 'meeting_id', 'meeting_password', 'scheduled_at',
    'duration_minutes', 'status', 'recurrence', 'is_public',
];

// Casts
protected $casts = [
    'scheduled_at'     => 'datetime',
    'duration_minutes' => 'integer',
    'is_public'        => 'boolean',
];
```

### Relationships

```php
// Belongs To
LiveClass → Tenant    (tenant())
LiveClass → Course    (course())
LiveClass → Subject   (subject())  // nullable
LiveClass → Chapter   (chapter())  // nullable
LiveClass → Lesson    (lesson())   // nullable
LiveClass → User      (creator())  // who scheduled it

// Has Many (reverse)
Course   → LiveClass[]  (liveClasses())
Subject  → LiveClass[]  (liveClasses())
Chapter  → LiveClass[]  (liveClasses())
Lesson   → LiveClass[]  (liveClasses())
```

### Computed Attributes

```php
// Level Label - where is this class attached?
$liveClass->level_label  // 'Lesson', 'Chapter', 'Subject', or 'Course'

// Time Calculations
$liveClass->ends_at       // Carbon: scheduled_at + duration_minutes
$liveClass->is_live_now   // bool: now between scheduled_at and ends_at
$liveClass->is_upcoming   // bool: scheduled_at is future
$liveClass->is_completed  // bool: status=completed OR ends_at past

// Platform Helpers
$liveClass->platform_label  // Human name (e.g., 'Google Meet')
$liveClass->platform_color  // CSS classes for badge
$liveClass->platform_icon   // Emoji icon

// Status Display
$liveClass->status_badge   // CSS classes
$liveClass->status_label   // 'LIVE NOW', 'Scheduled', 'Ended', etc.

// Secure URL with embedded password
$liveClass->secure_meeting_url  // URL with pwd param (Zoom) or hash (Jitsi)
```

### Scopes

```php
LiveClass::upcoming()     // status=scheduled AND scheduled_at > now
LiveClass::forCourse($id) // filter by course_id
LiveClass::public()       // is_public = true
```

---

## 4. Admin/Tenant Features

### CRUD Operations

| Feature | Route | Controller Method |
|---------|-------|-------------------|
| List classes | `GET /courses/{course}/live-classes` | `LiveClassController@index` |
| Create form | `GET /courses/{course}/live-classes/create` | `LiveClassController@create` |
| Store class | `POST /courses/{course}/live-classes` | `LiveClassController@store` |
| Edit form | `GET /courses/{course}/live-classes/{liveClass}/edit` | `LiveClassController@edit` |
| Update class | `PUT /courses/{course}/live-classes/{liveClass}` | `LiveClassController@update` |
| Delete class | `DELETE /courses/{course}/live-classes/{liveClass}` | `LiveClassController@destroy` |

### Status Management

| Action | Route | Description |
|--------|-------|-------------|
| Mark Live | `POST /.../mark-live` | Sets status='live', redirects to meeting URL, notifies students |
| End Live | `POST /.../end-live` | Sets status='completed', notifies students class ended |
| Mark Completed | `POST /.../mark-completed` | Manual completion without going live |

### Validation Rules (Store/Update)

```php
[
    'title'            => 'required|string|max:255',
    'description'      => 'nullable|string|max:1000',
    'platform'         => 'required|in:google_meet,zoom,ms_teams,jitsi,other',
    'meeting_url'      => 'required|url',
    'meeting_id'       => 'nullable|string|max:100',
    'meeting_password' => 'nullable|string|max:100',
    'scheduled_at'     => 'required|date|after:now',  // or just 'date' for update
    'duration_minutes' => 'required|integer|min:5|max:480',
    'recurrence'       => 'required|in:none,daily,weekly',
    'status'           => 'required|in:scheduled,live,completed,cancelled',
    'is_public'        => 'nullable|boolean',
    'subject_id'       => 'nullable|integer|exists:subjects,id',
    'chapter_id'       => 'nullable|integer|exists:chapters,id',
    'lesson_id'        => 'nullable|integer|exists:lessons,id',
]
```

### Auto-Notifications

| Trigger | Recipients | Content |
|---------|------------|---------|
| Class created (public) | All tenant students | "🎥 Public Live Class: {title}" |
| Class created (private) | Enrolled students only | Via `NotificationService::liveClassScheduled()` |
| Marked Live | Enrolled/public students | Via `NotificationService::liveClassStarted()` |
| Ended Live | Enrolled/public students | "✅ Class Ended: {title}" |
| Video uploaded | Enrolled/public students | "📹 Recorded: {title}" |

---

## 5. Student Features

### View: `GET /student/live-classes`

**Controller:** `Student\LiveClassController@index`

**Logic:**
1. Get student's enrolled course IDs (active/approved enrollments)
2. Fetch classes where:
   - `(course_id IN enrolled_courses) OR (is_public=true AND tenant_id=current)`
3. Split into 3 groups:
   - `$upcoming` - status=scheduled, scheduled_at > now
   - `$liveNow` - status=live
   - `$past` - status=completed/cancelled, limited to 30

**UI Sections:**
- Stats row: Live Now / Upcoming / Completed counts
- Live Now section (red pulsing indicator)
- Upcoming classes list
- Past classes (if any)

### Card Display

Each class card shows:
- Platform icon + name
- Title + description
- Schedule time (formatted)
- Duration
- Status badge (LIVE NOW pulses)
- Join button (links to `secure_meeting_url`)
- For past classes: Show recorded video link if available

---

## 6. Routes & Controllers

### Tenant Routes (Admin/Teacher)

```php
Route::prefix('courses/{course}/live-classes')->name('tenant.live_classes.')->group(function () {
    Route::get('/', [LiveClassController::class, 'index'])->name('index');
    Route::get('/create', [LiveClassController::class, 'create'])->name('create');
    Route::post('/', [LiveClassController::class, 'store'])->name('store');
    Route::get('/{liveClass}/edit', [LiveClassController::class, 'edit'])->name('edit');
    Route::put('/{liveClass}', [LiveClassController::class, 'update'])->name('update');
    Route::delete('/{liveClass}', [LiveClassController::class, 'destroy'])->name('destroy');
    
    // Status actions
    Route::post('/{liveClass}/mark-live', [LiveClassController::class, 'markLive'])->name('markLive');
    Route::post('/{liveClass}/end-live', [LiveClassController::class, 'endLive'])->name('endLive');
    Route::post('/{liveClass}/mark-completed', [LiveClassController::class, 'markCompleted'])->name('markCompleted');
    Route::post('/{liveClass}/upload-video', [LiveClassController::class, 'uploadVideo'])->name('uploadVideo');
});
```

### Teacher Routes (Same controller, different prefix)

```php
Route::prefix('courses/{course}/live-classes')->name('teacher.live_classes.')->group(function () {
    // Same routes as tenant above
});
```

### Student Routes

```php
Route::get('/live-classes', [StudentLiveClassController::class, 'index'])->name('student.live_classes.index');
```

---

## 7. Key Implementation Details

### Password Embedding (Auto-join)

```php
// LiveClass::getSecureMeetingUrlAttribute()

// Zoom: adds ?password=xxx
https://zoom.us/j/123456?password=secret

// Jitsi: adds #config.password=xxx
https://meet.jit.si/room-name#config.password=secret

// Google Meet/Teams: no password embedding (use waiting room)
```

### Permission System

```php
// Admin/Teacher authorization
private function authorizeCourse(Course $course): void
{
    if ($course->tenant_id !== Auth::user()->tenant_id) {
        abort(403);
    }
}

// Student viewing logic
$courseIds = $student->enrollments()
    ->whereIn('enrollment_status', ['active', 'approved'])
    ->pluck('course_id');

// Can view if:
// 1. Enrolled in the course, OR
// 2. Class is public AND same tenant
```

### Status Flow

```
scheduled → [markLive] → live → [endLive] → completed
    ↓                        ↓
[cancel]               [markCompleted]
    ↓                        ↓
cancelled              completed
```

### Video Recording Flow

1. Class ends (status → completed)
2. Admin uploads video URL via `uploadVideo()`
3. Students get notification with link
4. Video URL stored in `live_classes.video_url`
5. Students see "Watch Recording" button in past classes

---

## 8. Migration Guide for Jitsi

### Option A: Replace Existing System

If you want to **replace** the current platform-agnostic system with native Jitsi:

1. **Keep Database Schema** - Most fields still apply
2. **Modify Model**:
   - Remove `platform` enum (always 'jitsi')
   - `meeting_url` becomes auto-generated Jitsi room URL
   - Add `jitsi_room_name` field for room identification
   
3. **New Fields to Add**:
   ```php
   // For Jitsi JWT authentication
   $table->string('jitsi_jwt_token')->nullable();
   $table->boolean('requires_moderator_approval')->default(false);
   ```

4. **Controller Changes**:
   - Remove `meeting_url` from validation (auto-generate)
   - In `markLive()` - generate Jitsi JWT, embed in URL
   - Student `index()` - pass JWT-enabled URLs

5. **Views**:
   - Instead of "Join" button redirecting, optionally embed Jitsi iframe
   - Or keep redirect model but to JWT-signed URL

### Option B: Add Jitsi as New Platform

If you want to **add** Jitsi alongside existing platforms:

1. **Model** - No changes needed (`platform` already includes 'jitsi')
2. **Jitsi-specific enhancements**:
   - Add `jitsi_room_name` field
   - Add JWT generation logic in `getSecureMeetingUrlAttribute()`
   
3. **Admin UI**:
   - If platform=jitsi, show "Generate Room" button
   - Auto-fill meeting_url with `https://meet.jit.si/{room_name}`

### Recommended New Fields for Jitsi

```php
// Migration for Jitsi enhancement
Schema::table('live_classes', function (Blueprint $table) {
    // Room identification
    $table->string('jitsi_room_name')->nullable()->after('platform');
    
    // Authentication
    $table->text('jitsi_jwt_token')->nullable();
    $table->timestamp('jwt_expires_at')->nullable();
    
    // Moderator controls
    $table->boolean('is_moderated')->default(false);
    $table->json('moderator_settings')->nullable(); // lobby, recording, etc.
    
    // Recording (native)
    $table->string('jitsi_recording_id')->nullable();
    $table->timestamp('recording_started_at')->nullable();
    $table->timestamp('recording_ended_at')->nullable();
});
```

### Key Integration Points

| Current Feature | Jitsi Equivalent |
|-----------------|----------------|
| `meeting_url` | Jitsi room URL + JWT |
| `meeting_password` | Jitsi room password or lobby mode |
| `markLive()` | Start Jitsi room via API |
| `endLive()` | Stop recording, destroy room |
| `video_url` | Jitsi recording URL |
| `secure_meeting_url` | JWT-signed URL with claims |

### JWT Claims Structure for Jitsi

```php
[
    'iss' => 'bt_guru_app',           // App ID
    'aud' => 'jitsi',                 // Audience
    'sub' => 'meet.jit.si',           // Jitsi domain
    'room' => '*',                    // Room name wildcard or specific
    'exp' => time() + 3600,           // Expiry
    'moderator' => true/false,        // Is this user a moderator?
    'context' => [
        'user' => [
            'name' => 'Student Name',
            'email' => 'student@email.com',
            'avatar' => 'https://...',
        ]
    ]
]
```

---

## Files Reference

### Core Files

| Path | Description |
|------|-------------|
| `app/Models/LiveClass.php` | Model with all attributes, casts, computed properties |
| `app/Http/Controllers/Tenant/LiveClassController.php` | Admin CRUD + status management |
| `app/Http/Controllers/Student/LiveClassController.php` | Student viewing only |
| `app/Http/Controllers/Teacher/...` | (Uses same Tenant controller) |

### Views

| Path | Description |
|------|-------------|
| `resources/views/tenant/live_classes/index.blade.php` | Admin list view |
| `resources/views/tenant/live_classes/create.blade.php` | Create form |
| `resources/views/tenant/live_classes/edit.blade.php` | Edit form |
| `resources/views/tenant/live_classes/_card.blade.php` | Reusable class card |
| `resources/views/student/live_classes/index.blade.php` | Student mobile view |
| `resources/views/student/live_classes/_card_new.blade.php` | Student class card |

### Migrations

| Path | Description |
|------|-------------|
| `database/migrations/2025_05_24_000005_create_live_classes_table.php` | Base table |
| `database/migrations/2025_05_24_000006_add_level_ids_to_live_classes_table.php` | Curriculum FKs |
| `database/migrations/2025_05_28_000003_add_video_url_to_live_classes.php` | Recording field |
| `database/migrations/2025_05_28_000004_add_is_public_to_live_classes.php` | Public flag |

---

*End of Documentation*
