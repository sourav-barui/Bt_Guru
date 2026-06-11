<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BTLiveWhiteboardEvent extends Model
{
    use HasFactory;

    protected $table = 'btlive_whiteboard_events';

    protected $fillable = [
        'session_id',
        'pdf_id', // null if drawing on blank whiteboard
        'page_number', // which PDF page (if applicable)
        'event_type', // 'draw', 'erase', 'clear', 'text', 'highlight'
        'tool', // 'pen', 'highlighter', 'arrow', 'rectangle', 'circle', 'text', 'eraser'
        'tool_config', // color, width, opacity, etc.
        'data', // coordinates, paths, text content, etc.
        'created_by',
        'timestamp', // relative to session start (ms)
        'is_synced',
    ];

    protected $casts = [
        'tool_config' => 'json',
        'data' => 'json',
        'timestamp' => 'integer',
        'page_number' => 'integer',
        'is_synced' => 'boolean',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(BTLiveSession::class, 'session_id');
    }

    public function pdf()
    {
        return $this->belongsTo(BTLivePdf::class, 'pdf_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeForPdf($query, $pdfId)
    {
        return $query->where('pdf_id', $pdfId);
    }

    public function scopeForPage($query, $pageNumber)
    {
        return $query->where('page_number', $pageNumber);
    }

    public function scopeInOrder($query)
    {
        return $query->orderBy('timestamp');
    }

    public function scopeFromTimestamp($query, $timestamp)
    {
        return $query->where('timestamp', '>=', $timestamp);
    }

    // Helpers
    public static function getEventsForReplay(int $sessionId, int $fromTimestamp = 0): array
    {
        return self::where('session_id', $sessionId)
            ->where('timestamp', '>=', $fromTimestamp)
            ->orderBy('timestamp')
            ->get()
            ->map(fn($event) => [
                'id' => $event->id,
                'type' => $event->event_type,
                'tool' => $event->tool,
                'config' => $event->tool_config,
                'data' => $event->data,
                'timestamp' => $event->timestamp,
                'pdf_id' => $event->pdf_id,
                'page' => $event->page_number,
            ])
            ->toArray();
    }
}
