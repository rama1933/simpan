<?php

namespace App\Repositories\Knowledge;

use App\Models\Knowledge;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class KnowledgeRepository extends BaseRepository implements KnowledgeRepositoryInterface
{
    public function __construct(Knowledge $model)
    {
        parent::__construct($model);
    }

    /**
     * Get knowledge by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)->get();
    }

    /**
     * Get knowledge by category
     */
    public function getByCategory(string $category): Collection
    {
        return $this->model->where('category', $category)->get();
    }

    /**
     * Search knowledge by title or content
     */
    public function search(string $query): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('title', 'like', "%{$query}%")
                ->orWhere('content', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        })->get();
    }

    /**
     * Get knowledge by author
     */
    public function getByAuthor(int $authorId): Collection
    {
        return $this->model->where('author_id', $authorId)->get();
    }

    /**
     * Get knowledge with tags
     */
    public function getWithTags(array $tags): Collection
    {
        return $this->model->whereJsonContains('tags', $tags)->get();
    }

    /**
     * Get published knowledge for public view
     */
    public function getPublishedKnowledge(): Collection
    {
        return $this->model->where('status', 'published')
            ->with('author')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get knowledge statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => $this->model->count(),
            'published' => $this->model->where('status', 'published')->count(),
            'draft' => $this->model->where('status', 'draft')->count(),
            'archived' => $this->model->where('status', 'archived')->count(),
            'categories' => $this->model->distinct('category')->pluck('category')->count(),
        ];
    }

    /**
     * Get knowledge with pagination and filters
     */
    public function getWithFilters(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with('author');

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (isset($filters['author_id'])) {
            $query->where('author_id', $filters['author_id']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('content', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['tags']) && is_array($filters['tags'])) {
            foreach ($filters['tags'] as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        }

        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';

        return $query->orderBy($orderBy, $orderDirection)->paginate($perPage);
    }
}

