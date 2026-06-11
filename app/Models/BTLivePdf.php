<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BTLivePdf extends Model
{
    use HasFactory;

    protected $table = 'btlive_pdfs';

    protected $fillable = [
        'session_id',
        'uploaded_by',
        'title',
        'file_path',
        'file_size',
        'total_pages',
        'current_page',
        'is_active',
        'display_order',
        'annotations', // JSON of pre-existing annotations
    ];

    protected $casts = [
        'annotations' => 'json',
        'is_active' => 'boolean',
        'total_pages' => 'integer',
        'current_page' => 'integer',
        'display_order' => 'integer',
        'file_size' => 'integer',
    ];

    // Relationships
    public function session()
    {
        return $this->belongsTo(BTLiveSession::class, 'session_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function annotations()
    {
        return $this->hasMany(BTLiveWhiteboardEvent::class, 'pdf_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    // Helpers
    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function activate()
    {
        // Deactivate other PDFs in this session
        $this->session->pdfs()->update(['is_active' => false]);
        
        $this->update(['is_active' => true]);
        
        // Update session current_pdf
        $this->session->update([
            'current_pdf_id' => $this->id,
            'current_pdf_page' => 1,
        ]);
    }
}
