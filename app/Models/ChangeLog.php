<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChangeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'description',
        'metadata',
        'user_id',
        'ip_address',
        'user_agent',
        'changed_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'changed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the user who made the change
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the entity that was changed
     */
    public function entity(): MorphTo
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id');
    }

    /**
     * Scope untuk filter berdasarkan entity type
     */
    public function scopeForEntity($query, string $entityType, int $entityId = null)
    {
        $query->where('entity_type', $entityType);
        
        if ($entityId) {
            $query->where('entity_id', $entityId);
        }
        
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk filter berdasarkan user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk filter berdasarkan tanggal
     */
    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('changed_at', [$startDate, $endDate]);
    }

    /**
     * Get formatted change description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        if ($this->description) {
            return $this->description;
        }

        $userName = $this->user?->name ?? 'System';
        $entityName = class_basename($this->entity_type);
        
        switch ($this->action) {
            case 'created':
                return "{$userName} membuat {$entityName} baru";
            case 'updated':
                return "{$userName} memperbarui {$entityName}";
            case 'deleted':
                return "{$userName} menghapus {$entityName}";
            case 'published':
                return "{$userName} mempublikasikan {$entityName}";
            case 'archived':
                return "{$userName} mengarsipkan {$entityName}";
            default:
                return "{$userName} melakukan {$this->action} pada {$entityName}";
        }
    }

    /**
     * Get change summary for display
     */
    public function getChangeSummaryAttribute(): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'description' => $this->formatted_description,
            'field_name' => $this->field_name,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'user_name' => $this->user?->name ?? 'System',
            'changed_at' => $this->changed_at,
            'metadata' => $this->metadata
        ];
    }
}