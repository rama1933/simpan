<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Knowledge extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'description',
        'tags',
        'category_id',
        'skpd_id',
        'author_id',
        'status',
        'published_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];

    /**
     * Get the author that owns the knowledge.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the category that owns the knowledge.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the SKPD that owns the knowledge.
     */
    public function skpd()
    {
        return $this->belongsTo(MasterSKPD::class, 'skpd_id');
    }

    public function tagsRelation(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'knowledge_tag');
    }

    public function attachments()
    {
        return $this->hasMany(KnowledgeAttachment::class);
    }

    /**
     * Scope a query to only include published knowledge.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include draft knowledge.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include archived knowledge.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    /**
     * Scope a query to search in title, content, and description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->whereHas('category', function ($q) use ($category) {
            $q->where('name', $category);
        });
    }

    /**
     * Scope a query to filter by author.
     */
    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    /**
     * Scope a query to filter by tags.
     */
    public function scopeWithTags($query, array $tags)
    {
        foreach ($tags as $tag) {
            $query->whereJsonContains('tags', $tag);
        }
        return $query;
    }

    /**
     * Get the knowledge status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'published' => 'green',
            'draft' => 'yellow',
            'archived' => 'gray',
            default => 'blue'
        };
    }

    /**
     * Get the knowledge status badge text.
     */
    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'published' => 'Dipublikasi',
            'draft' => 'Draft',
            'archived' => 'Diarsipkan',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Check if the knowledge is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if the knowledge is draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the knowledge is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Check if the user can edit this knowledge.
     */
    public function canEdit(User $user): bool
    {
        return $user->id === $this->author_id || $user->hasRole('admin');
    }

    /**
     * Check if the user can delete this knowledge.
     */
    public function canDelete(User $user): bool
    {
        return $user->id === $this->author_id || $user->hasRole('admin');
    }
}
