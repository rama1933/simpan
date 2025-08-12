<?php

namespace App\Repositories\Knowledge;

use App\Models\KnowledgeVersion;
use App\Repositories\BaseRepository;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class KnowledgeVersionRepository extends BaseRepository implements KnowledgeVersionRepositoryInterface
{
    public function __construct(KnowledgeVersion $model)
    {
        parent::__construct($model);
    }

    /**
     * Get versions by knowledge ID
     */
    public function getByKnowledgeId(int $knowledgeId): Collection
    {
        return $this->model->where('knowledge_id', $knowledgeId)
            ->with(['creator', 'verifier', 'attachments', 'tags'])
            ->orderBy('version_number', 'desc')
            ->get();
    }

    /**
     * Get versions by status
     */
    public function getByStatus(string $status): Collection
    {
        return $this->model->where('status', $status)
            ->with(['knowledge', 'creator', 'category', 'skpd'])
            ->latest()
            ->get();
    }

    /**
     * Get versions by verification status
     */
    public function getByVerificationStatus(string $verificationStatus): Collection
    {
        return $this->model->where('verification_status', $verificationStatus)
            ->with(['knowledge', 'creator', 'verifier'])
            ->latest()
            ->get();
    }

    /**
     * Get current version for knowledge
     */
    public function getCurrentVersion(int $knowledgeId)
    {
        return $this->model->where('knowledge_id', $knowledgeId)
            ->where('is_current', true)
            ->with(['creator', 'verifier', 'attachments', 'tags'])
            ->first();
    }

    /**
     * Get published versions
     */
    public function getPublishedVersions(): Collection
    {
        return $this->model->where('status', 'published')
            ->with(['knowledge', 'category', 'skpd', 'creator'])
            ->latest()
            ->get();
    }

    /**
     * Get versions with filters
     */
    public function getWithFilters(array $filters): LengthAwarePaginator
    {
        $query = $this->model->with([
            'knowledge:id,title',
            'category:id,name',
            'skpd:id,nama_skpd as name',
            'creator:id,name',
            'verifier:id,name'
        ]);

        // Filter by knowledge
        if (isset($filters['knowledge_id'])) {
            $query->where('knowledge_id', $filters['knowledge_id']);
        }

        // Filter by status
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Filter by verification status
        if (isset($filters['verification_status'])) {
            $query->where('verification_status', $filters['verification_status']);
        }

        // Filter by category
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Filter by SKPD
        if (isset($filters['skpd_id'])) {
            $query->where('skpd_id', $filters['skpd_id']);
        }

        // Filter by creator
        if (isset($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        // Filter by date range
        if (isset($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (isset($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        // Search
        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('summary', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('change_reason', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get version statistics
     */
    public function getStatistics(): array
    {
        $total = $this->model->count();
        $published = $this->model->where('status', 'published')->count();
        $draft = $this->model->where('status', 'draft')->count();
        $archived = $this->model->where('status', 'archived')->count();
        $withdrawn = $this->model->where('status', 'withdrawn')->count();
        
        $pending = $this->model->where('verification_status', 'pending')->count();
        $verified = $this->model->where('verification_status', 'verified')->count();
        $rejected = $this->model->where('verification_status', 'rejected')->count();

        return [
            'total' => $total,
            'status' => [
                'published' => $published,
                'draft' => $draft,
                'archived' => $archived,
                'withdrawn' => $withdrawn,
            ],
            'verification' => [
                'pending' => $pending,
                'verified' => $verified,
                'rejected' => $rejected,
            ],
            'percentages' => [
                'published' => $total > 0 ? round(($published / $total) * 100, 1) : 0,
                'verified' => $total > 0 ? round(($verified / $total) * 100, 1) : 0,
            ]
        ];
    }

    /**
     * Search versions
     */
    public function search(string $query): Collection
    {
        return $this->model->where(function ($q) use ($query) {
            $q->where('title', 'like', '%' . $query . '%')
              ->orWhere('content', 'like', '%' . $query . '%')
              ->orWhere('summary', 'like', '%' . $query . '%')
              ->orWhere('change_reason', 'like', '%' . $query . '%');
        })
        ->with(['knowledge', 'creator', 'category'])
        ->latest()
        ->get();
    }

    /**
     * Get versions by creator
     */
    public function getByCreator(int $creatorId): Collection
    {
        return $this->model->where('created_by', $creatorId)
            ->with(['knowledge', 'category', 'skpd'])
            ->latest()
            ->get();
    }

    /**
     * Get versions by verifier
     */
    public function getByVerifier(int $verifierId): Collection
    {
        return $this->model->where('verified_by', $verifierId)
            ->with(['knowledge', 'creator'])
            ->latest()
            ->get();
    }

    /**
     * Get versions by date range
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])
        ->with(['knowledge', 'creator'])
        ->latest()
        ->get();
    }

    /**
     * Get pending verification versions
     */
    public function getPendingVerification(): Collection
    {
        return $this->model->where('verification_status', 'pending')
            ->with(['knowledge', 'creator', 'category', 'skpd'])
            ->oldest('created_at')
            ->get();
    }

    /**
     * Get expired versions
     */
    public function getExpiredVersions(): Collection
    {
        return $this->model->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now())
            ->where('status', 'published')
            ->with(['knowledge', 'creator'])
            ->get();
    }

    /**
     * Get versions with attachments
     */
    public function getWithAttachments(): Collection
    {
        return $this->model->has('attachments')
            ->with(['knowledge', 'creator', 'attachments'])
            ->latest()
            ->get();
    }
}