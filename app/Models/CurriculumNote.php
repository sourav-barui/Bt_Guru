<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CurriculumNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'noteable_id',
        'noteable_type',
        'title',
        'file_path',
        'file_type',
        'is_downloadable',
        'order',
        'user_id',
        'available_from',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_downloadable' => 'boolean',
        'available_from' => 'date',
    ];

    public function noteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $size = 0;
        $path = storage_path('app/public/' . $this->file_path);
        if (file_exists($path)) {
            $size = filesize($path);
        }
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $unitIndex = 0;
        
        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }
        
        return round($size, 2) . ' ' . $units[$unitIndex];
    }
}
