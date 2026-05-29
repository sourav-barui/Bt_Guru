<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'description',
        'author',
        'publisher',
        'isbn',
        'type',
        'pdf_price',
        'physical_price',
        'cover_image',
        'pdf_file',
        'stock_quantity',
        'status',
        'metadata',
    ];

    protected $casts = [
        'pdf_price' => 'decimal:2',
        'physical_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'pdf_price' => 0,
        'physical_price' => 0,
        'stock_quantity' => 0,
        'status' => 'active',
        'type' => 'pdf',
        'metadata' => '[]',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(BookOrder::class);
    }

    public function paymentRequests(): HasMany
    {
        return $this->hasMany(PaymentRequest::class);
    }

    public function scopeForTenant($query, int $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePdf($query)
    {
        return $query->whereIn('type', ['pdf', 'both']);
    }

    public function scopePhysical($query)
    {
        return $query->whereIn('type', ['physical', 'both']);
    }

    public function isPdf(): bool
    {
        return in_array($this->type, ['pdf', 'both']);
    }

    public function isPhysical(): bool
    {
        return in_array($this->type, ['physical', 'both']);
    }

    public function isBoth(): bool
    {
        return $this->type === 'both';
    }

    public function getDisplayPriceAttribute(): string
    {
        if ($this->type === 'pdf') {
            return '₹' . number_format($this->pdf_price, 2);
        } elseif ($this->type === 'physical') {
            return '₹' . number_format($this->physical_price, 2);
        } else {
            return 'PDF: ₹' . number_format($this->pdf_price, 2) . ' | Physical: ₹' . number_format($this->physical_price, 2);
        }
    }

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('images/default-book-cover.png');
    }

    public function getPdfUrlAttribute(): ?string
    {
        if ($this->pdf_file) {
            return asset('storage/' . $this->pdf_file);
        }
        return null;
    }

    public function isInStock(): bool
    {
        if (!$this->isPhysical()) {
            return true;
        }
        return $this->stock_quantity > 0;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'draft' => 'secondary',
            default => 'secondary',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'pdf' => 'PDF Only',
            'physical' => 'Physical Only',
            'both' => 'PDF & Physical',
            default => ucfirst($this->type),
        };
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title);
            }
        });
    }
}
