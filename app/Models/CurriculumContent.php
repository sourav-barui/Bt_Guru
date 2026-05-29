<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CurriculumContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'contentable_id',
        'contentable_type',
        'title',
        'description',
        'video_url',
        'video_type',
        'order',
        'user_id',
        'available_from',
    ];

    protected $casts = [
        'order' => 'integer',
        'available_from' => 'date',
    ];

    public function contentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) return null;
        
        return match($this->video_type) {
            'youtube' => $this->getYouTubeEmbedUrl($this->video_url),
            'vimeo' => $this->getVimeoEmbedUrl($this->video_url),
            default => $this->video_url,
        };
    }

    private function getYouTubeEmbedUrl(string $url): string
    {
        $videoId = '';
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\s]+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        return "https://www.youtube.com/embed/{$videoId}";
    }

    private function getVimeoEmbedUrl(string $url): string
    {
        $videoId = '';
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            $videoId = $matches[1];
        }
        return "https://player.vimeo.com/video/{$videoId}";
    }
}
