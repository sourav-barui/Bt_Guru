<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $table = 'system_settings';
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key, with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("sys_setting_{$key}", 300, function () use ($key, $default) {
            $row = static::where('key', $key)->first();
            return $row ? $row->value : $default;
        });
    }

    /**
     * Set (upsert) a setting value, clear cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("sys_setting_{$key}");
    }

    /**
     * Set many keys at once from array.
     */
    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            static::set($key, $value);
        }
    }

    /**
     * Return all mail settings as an associative array.
     */
    public static function mailConfig(): array
    {
        return [
            'driver'       => static::get('mail_driver', 'smtp'),
            'host'         => static::get('mail_host', ''),
            'port'         => (int) static::get('mail_port', 587),
            'username'     => static::get('mail_username', ''),
            'password'     => static::get('mail_password', ''),
            'encryption'   => static::get('mail_encryption', 'tls'),
            'from_address' => static::get('mail_from_address', ''),
            'from_name'    => static::get('mail_from_name', 'BT Guru'),
        ];
    }

    /**
     * Check if system mail is configured.
     */
    public static function isMailConfigured(): bool
    {
        return !empty(static::get('mail_host')) && !empty(static::get('mail_username'));
    }
}
