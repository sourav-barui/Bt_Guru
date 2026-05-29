<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TenantRegistration extends Model
{
    protected $fillable = ['data', 'token', 'otp', 'otp_expires_at', 'email_verified', 'step'];

    protected $casts = [
        'data'           => 'array',
        'email_verified' => 'boolean',
        'otp_expires_at' => 'datetime',
    ];

    public static function startNew(): self
    {
        return self::create([
            'data'  => [],
            'token' => Str::random(64),
            'step'  => 1,
        ]);
    }

    public static function findByToken(string $token): ?self
    {
        return self::where('token', $token)->first();
    }

    public function mergeData(array $new): void
    {
        $this->data = array_merge($this->data ?? [], $new);
        $this->save();
    }

    public function generateOtp(): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->otp            = $otp;
        $this->otp_expires_at = now()->addMinutes(15);
        $this->save();
        return $otp;
    }

    public function isOtpValid(string $otp): bool
    {
        return $this->otp === $otp && $this->otp_expires_at?->isFuture();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}
