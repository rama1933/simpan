<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class KnowledgeVersion extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_id',
        'version_number',
        'title',
        'content',
        'summary',
        'category_id',
        'skpd_id',
        'status',
        'verification_status',
        'verification_notes',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'change_type',
        'change_reason',
        'metadata',
        'created_by',
        'replaced_by_version_id',
        'effective_date',
        'expiry_date',
        'is_current',
    ];

    protected $casts = [
        'metadata' => 'array',
        'verified_at' => 'datetime',
        'effective_date' => 'datetime',
        'expiry_date' => 'datetime',
        'is_current' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the knowledge that owns this version
     */
    public function knowledge(): BelongsTo
    {
        return $this->belongsTo(Knowledge::class);
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the SKPD
     */
    public function skpd(): BelongsTo
    {
        return $this->belongsTo(MasterSKPD::class, 'skpd_id');
    }

    /**
     * Get the user who created this version
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who verified this version
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the version that replaced this version
     */
    public function replacedByVersion(): BelongsTo
    {
        return $this->belongsTo(KnowledgeVersion::class, 'replaced_by_version_id');
    }

    /**
     * Get versions that this version replaced
     */
    public function replacedVersions(): HasMany
    {
        return $this->hasMany(KnowledgeVersion::class, 'replaced_by_version_id');
    }

    /**
     * Get the attachments for this version
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(KnowledgeVersionAttachment::class);
    }

    /**
     * Get the tags for this version
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'knowledge_version_tag');
    }

    /**
     * Scope untuk versi yang aktif
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope untuk versi yang dipublikasikan
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope untuk versi yang sudah diverifikasi
     */
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    /**
     * Scope untuk versi yang masih berlaku
     */
    public function scopeEffective($query)
    {
        $now = now();
        return $query->where(function ($q) use ($now) {
            $q->whereNull('effective_date')
              ->orWhere('effective_date', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('expiry_date')
              ->orWhere('expiry_date', '>', $now);
        });
    }

    /**
     * Check if this version is effective
     */
    public function isEffective(): bool
    {
        $now = now();
        $effectiveCheck = !$this->effective_date || $this->effective_date <= $now;
        $expiryCheck = !$this->expiry_date || $this->expiry_date > $now;
        
        return $effectiveCheck && $expiryCheck;
    }

    /**
     * Get version display name
     */
    public function getVersionDisplayAttribute(): string
    {
        return "v{$this->version_number}";
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'bg-gray-100 text-gray-800',
            'published' => 'bg-green-100 text-green-800',
            'archived' => 'bg-yellow-100 text-yellow-800',
            'withdrawn' => 'bg-red-100 text-red-800',
            'replaced' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    /**
     * Get verification status badge class
     */
    public function getVerificationBadgeClassAttribute(): string
    {
        return match($this->verification_status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'verified' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }
}
