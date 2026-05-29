<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'coaching_name',
        'slug',
        'subdomain',
        'custom_domain',
        'logo',
        'pwa_icon',
        'portal_icon',
        'email',
        'phone',
        'address',
        'status',
        'expires_at',
        'settings',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'settings' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'pending',
        'settings' => '[]',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeBySubdomain($query, string $subdomain)
    {
        return $query->where('subdomain', $subdomain);
    }

    public function scopeByCustomDomain($query, string $domain)
    {
        return $query->where('custom_domain', $domain);
    }

    public function getFullUrlAttribute(): string
    {
        if ($this->custom_domain) {
            return 'https://' . $this->custom_domain;
        }
        return 'https://' . $this->subdomain . config('app.tenant_subdomain_suffix');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function adminUsers(): HasMany
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('name', 'tenant_admin');
        });
    }

    public function teachers(): HasMany
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('name', 'teacher');
        });
    }

    public function students(): HasMany
    {
        return $this->users()->whereHas('roles', function ($query) {
            $query->where('name', 'student');
        });
    }
}
