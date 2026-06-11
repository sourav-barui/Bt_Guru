<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'chapter_id',
        'title',
        'description',
        'video_url',
        'video_type',
        'duration_minutes',
        'order',
        'status',
    ];

    protected $casts = [
        'order' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class)->orderBy('scheduled_at');
    }

    public function btliveSessions(): HasMany
    {
        return $this->hasMany(BTLiveSession::class)->orderBy('scheduled_at');
    }

    public function contents(): MorphMany
    {
        return $this->morphMany(CurriculumContent::class, 'contentable')->orderBy('order');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(CurriculumNote::class, 'noteable')->orderBy('order');
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class)->orderBy('created_at', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
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
