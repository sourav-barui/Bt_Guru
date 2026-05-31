<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveClassAttendance extends Model
{
    use HasFactory;

    protected $table = 'live_class_attendance';

    protected $fillable = [
        'tenant_id',
        'live_class_id',
        'student_id',
        'joined_at',
        'left_at',
        'duration_seconds',
        'ip_address',
        'device_type',
        'browser',
        'os',
        'jitsi_participant_id',
        'display_name',
        'was_kicked',
        'kick_reason',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'duration_seconds' => 'integer',
        'was_kicked' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function liveClass(): BelongsTo
    {
        return $this->belongsTo(LiveClass::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
