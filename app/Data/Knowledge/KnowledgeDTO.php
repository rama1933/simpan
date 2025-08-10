<?php

namespace App\Data\Knowledge;

use App\Data\BaseDTO;

class KnowledgeDTO extends BaseDTO
{
    public function __construct(
        public ?int $id = null,
        public string $title = '',
        public string $content = '',
        public string $description = '',
        public string $category = '',
        public array $tags = [],
        public string $status = 'draft',
        public ?int $author_id = null,
        public ?string $author_name = null,
        public ?string $created_at = null,
        public ?string $updated_at = null
    ) {
    }

    /**
     * Create DTO from model
     */
    public static function fromModel($model): static
    {
        return new static(
            id: $model->id,
            title: $model->title,
            content: $model->content,
            description: $model->description,
            category: $model->category,
            tags: $model->tags ?? [],
            status: $model->status,
            author_id: $model->author_id,
            author_name: $model->author?->name,
            created_at: $model->created_at?->toISOString(),
            updated_at: $model->updated_at?->toISOString()
        );
    }

    /**
     * Get validation rules
     */
    public static function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'description' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => 'required|in:draft,published,archived'
        ];
    }

    /**
     * Get fillable fields for create/update
     */
    public function getFillable(): array
    {
        return [
            'title',
            'content',
            'description',
            'category',
            'tags',
            'status'
        ];
    }
}

