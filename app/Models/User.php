<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, HasRoles, HasApiTokens;

    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'email_verified_at',
        'password',
        'avatar',
        'status',
        'current_session_id',
        'last_login_ip',
        'last_login_at',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'current_session_id',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password_changed_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function taughtCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_teacher', 'teacher_id', 'course_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    // Alias for taughtCourses - used in views
    public function courses(): BelongsToMany
    {
        return $this->taughtCourses();
    }

    public function approvedEnrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'approved_by');
    }

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class, 'created_by');
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuperAdmins($query)
    {
        return $query->whereNull('tenant_id')
            ->whereHas('roles', function ($q) {
                $q->where('name', 'super_admin');
            });
    }

    public function isSuperAdmin(): bool
    {
        return $this->tenant_id === null && $this->hasRole('super_admin');
    }

    public function isTenantAdmin(): bool
    {
        return $this->hasRole('tenant_admin');
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('teacher');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('student');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=random';
    }

    public function getRoleDisplayNameAttribute(): string
    {
        $role = $this->roles->first();
        return $role ? ucwords(str_replace('_', ' ', $role->name)) : 'User';
    }

    /**
     * Check if user has an active session
     */
    public function hasActiveSession(): bool
    {
        if (!$this->current_session_id) {
            return false;
        }

        // Check if session exists in database
        $session = \DB::table('sessions')
            ->where('id', $this->current_session_id)
            ->first();

        return $session !== null;
    }

    /**
     * Update current session info
     */
    public function updateSessionInfo(string $sessionId): void
    {
        // Use direct DB query to bypass global scopes
        \DB::table('users')
            ->where('id', $this->id)
            ->update([
                'current_session_id' => $sessionId,
                'last_login_ip' => request()->ip(),
                'last_login_at' => now(),
            ]);

        // Refresh model attributes
        $this->refresh();
    }

    /**
     * Clear all sessions and logout from all devices
     */
    public function logoutFromAllDevices(): void
    {
        // Delete all sessions for this user from the sessions table
        \DB::table('sessions')
            ->where('user_id', $this->id)
            ->delete();

        // Clear current session tracking using DB query to bypass global scopes
        \DB::table('users')
            ->where('id', $this->id)
            ->update([
                'current_session_id' => null,
                'last_login_at' => null,
                'last_login_ip' => null,
            ]);

        // Refresh the model attributes
        $this->refresh();
    }

    /**
     * Invalidate old sessions after password change
     */
    public function invalidateOldSessions(): void
    {
        $this->logoutFromAllDevices();
        $this->update([
            'password_changed_at' => now(),
        ]);
    }

    protected static function booted(): void
    {
        static::addGlobalScope('tenant', function ($query) {
            if (session()->has('current_tenant_id')) {
                $query->where('tenant_id', session('current_tenant_id'));
            }
        });
    }
}
