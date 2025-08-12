<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class KnowledgeVersionAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'knowledge_version_id',
        'filename',
        'original_filename',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'description',
        'attachment_type',
        'is_primary',
        'metadata',
        'uploaded_by'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_primary' => 'boolean',
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the knowledge version that owns this attachment
     */
    public function knowledgeVersion(): BelongsTo
    {
        return $this->belongsTo(KnowledgeVersion::class);
    }

    /**
     * Get the user who uploaded this attachment
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get the full URL for the attachment
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    /**
     * Get formatted file size
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if attachment is an image
     */
    public function isImage(): bool
    {
        return $this->attachment_type === 'image' || 
               str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if attachment is a document
     */
    public function isDocument(): bool
    {
        return $this->attachment_type === 'document';
    }

    /**
     * Scope untuk attachment utama
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope berdasarkan tipe attachment
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('attachment_type', $type);
    }
}
