<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certification extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'issuer',
        'credential_id',
        'credential_url',
        'badge_image',
        'issued_at',
        'expires_at',
        'is_featured',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'issued_at'  => 'date',
        'expires_at' => 'date',
        'is_featured' => 'boolean',
        'sort_order'  => 'integer',
    ];

    /**
     * Scope: only featured certifications.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope: order by sort_order asc, then issued_at desc.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderByDesc('issued_at');
    }

    /**
     * Scope: certifications that have not expired.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>=', now());
        });
    }

    /**
     * Determine if the certification is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    /**
     * Get the full URL to the badge image.
     */
    public function getBadgeUrlAttribute(): ?string
    {
        if (! $this->badge_image) {
            return null;
        }

        return asset('storage/' . $this->badge_image);
    }
}
