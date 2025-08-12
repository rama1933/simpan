<?php

namespace App\Services\Knowledge;

use App\Models\Knowledge;
use App\Models\Category;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Tag;
use Illuminate\Support\Str;
use App\Models\MasterTag;
use App\Models\KnowledgeAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class KnowledgeService
{
    /**
     * Get overall knowledge statistics
     */
    public function getKnowledgeStatistics()
    {
        $total = Knowledge::count();
        $published = Knowledge::where('status', 'published')->count();
        $draft = Knowledge::where('status', 'draft')->count();
        $archived = Knowledge::where('status', 'archived')->count();

        return [
            'total' => $total,
            'published' => $published,
            'draft' => $draft,
            'archived' => $archived,
            'published_percentage' => $total > 0 ? round(($published / $total) * 100, 1) : 0,
            'draft_percentage' => $total > 0 ? round(($draft / $total) * 100, 1) : 0,
            'archived_percentage' => $total > 0 ? round(($archived / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get category distribution
     */
    public function getCategoryDistribution()
    {
        return Category::withCount('knowledge')
            ->get()
            ->map(function ($category) {
                $total = Knowledge::count();
                return [
                    'name' => $category->name,
                    'count' => $category->knowledge_count,
                    'percentage' => $total > 0 ? round(($category->knowledge_count / $total) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('count')
            ->values();
    }

    /**
     * Get status distribution
     */
    public function getStatusDistribution()
    {
        $total = Knowledge::count();

        return [
            'published' => [
                'count' => Knowledge::where('status', 'published')->count(),
                'percentage' => $total > 0 ? round((Knowledge::where('status', 'published')->count() / $total) * 100, 1) : 0,
            ],
            'draft' => [
                'count' => Knowledge::where('status', 'draft')->count(),
                'percentage' => $total > 0 ? round((Knowledge::where('status', 'draft')->count() / $total) * 100, 1) : 0,
            ],
            'archived' => [
                'count' => Knowledge::where('status', 'archived')->count(),
                'percentage' => $total > 0 ? round((Knowledge::where('status', 'archived')->count() / $total) * 100, 1) : 0,
            ],
        ];
    }

    /**
     * Get author statistics
     */
    public function getAuthorStatistics()
    {
        $total = Knowledge::count();

        return User::withCount(['knowledge as total_articles'])
            ->withCount([
                'knowledge as published_articles' => function ($query) {
                    $query->where('status', 'published');
                }
            ])
            ->withCount([
                'knowledge as draft_articles' => function ($query) {
                    $query->where('status', 'draft');
                }
            ])
            ->withCount([
                'knowledge as archived_articles' => function ($query) {
                    $query->where('status', 'archived');
                }
            ])
            ->having('total_articles', '>', 0)
            ->get()
            ->map(function ($user) use ($total) {
                return [
                    'name' => $user->name,
                    'email' => $user->email,
                    'total_articles' => $user->total_articles,
                    'published_articles' => $user->published_articles,
                    'draft_articles' => $user->draft_articles,
                    'archived_articles' => $user->archived_articles,
                    'contribution_percentage' => $total > 0 ? round(($user->total_articles / $total) * 100, 1) : 0,
                ];
            })
            ->sortByDesc('total_articles')
            ->values();
    }

    /**
     * Get monthly creation trends
     */
    public function getMonthlyTrends()
    {
        $months = collect();
        $currentDate = Carbon::now()->startOfYear();
        $endDate = Carbon::now()->endOfYear();

        while ($currentDate->lte($endDate)) {
            $monthName = $currentDate->format('M');
            $monthKey = $currentDate->format('Y-m');

            $count = Knowledge::whereYear('created_at', $currentDate->year)
                ->whereMonth('created_at', $currentDate->month)
                ->count();

            $months->push([
                'month' => $monthName,
                'count' => $count,
                'year_month' => $monthKey,
            ]);

            $currentDate->addMonth();
        }

        $maxCount = $months->max('count');

        return $months->map(function ($month) use ($maxCount) {
            $month['percentage'] = $maxCount > 0 ? round(($month['count'] / $maxCount) * 100, 1) : 0;
            return $month;
        });
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities($limit = 10)
    {
        return ActivityLog::with(['subject', 'causer'])
            ->where('subject_type', Knowledge::class)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'action' => $activity->action,
                    'description' => $activity->description,
                    'subject_title' => $activity->subject?->title ?? 'Unknown',
                    'causer_name' => $activity->causer?->name ?? 'System',
                    'created_at' => $activity->created_at,
                    'properties' => $activity->properties,
                ];
            });
    }

    /**
     * Get all knowledge with filters and pagination
     */
    public function getAllKnowledge($filters = [], $perPage = 15)
    {
        $query = Knowledge::with(['category', 'author', 'skpd'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('content', 'like', '%' . $filters['search'] . '%');
            });
        }

        // Apply category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply SKPD filter from request
        if (!empty($filters['skpd_id'])) {
            $query->where('skpd_id', $filters['skpd_id']);
        }

        // Enforce SKPD restriction for non-admin (User SKPD)
        $user = Auth::user();
        if ($user instanceof User) {
            if ($user->hasRole('User SKPD') && $user->skpd_id) {
                $query->where('skpd_id', $user->skpd_id);
            }
        }

        // Apply verification status filter
        if (!empty($filters['verification_status'])) {
            $query->where('verification_status', $filters['verification_status']);
        }

        // Apply tags filter (expects array of tag IDs)
        if (!empty($filters['tags'])) {
            $tagIds = is_array($filters['tags']) ? array_filter($filters['tags']) : explode(',', (string) $filters['tags']);
            if (!empty($tagIds)) {
                $query->whereHas('tagsRelation', function ($q) use ($tagIds) {
                    $q->whereIn('tags.id', $tagIds);
                });
            }
        }

        return [
            'success' => true,
            'data' => $query->paginate($perPage)
        ];
    }

    /**
     * Get all categories
     */
    public function getCategories()
    {
        return \App\Models\Category::where('is_active', true)->get();
    }

    /**
     * Get all SKPDs
     */
    public function getSKPDs()
    {
        return \App\Models\MasterSKPD::where('is_active', true)->get();
    }

    /**
     * Get knowledge by ID
     */
    public function getKnowledgeById($id)
    {
        $knowledge = Knowledge::with(['category', 'author', 'skpd', 'attachments'])->find($id);

        if (!$knowledge) {
            return [
                'success' => false,
                'message' => 'Knowledge tidak ditemukan'
            ];
        }

        // Restrict SKPD access for non-admin
        $user = Auth::user();
        if ($user instanceof User) {
            if ($user->hasRole('User SKPD')) {
                if ((int) $knowledge->skpd_id !== (int) $user->skpd_id) {
                    return [
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke data ini'
                    ];
                }
            }
        }

        return [
            'success' => true,
            'data' => $knowledge
        ];
    }

    /**
     * Create new knowledge
     */
    public function createKnowledge($dto)
    {
        try {
            // Determine SKPD ID: force for User SKPD
            $skpdId = $dto->skpd_id;
            $user = Auth::user();
            if ($user instanceof User) {
                if ($user->hasRole('User SKPD') && $user->skpd_id) {
                    $skpdId = $user->skpd_id;
                }
            }

            $knowledge = Knowledge::create([
                'title' => $dto->title,
                'content' => $dto->content,
                'description' => $dto->description,
                'tags' => $dto->tags,
                'status' => $dto->status ?? 'draft',
                'author_id' => Auth::id(),
                'category_id' => $dto->category_id,
                'skpd_id' => $skpdId,
                'verification_status' => 'pending',
                'published_at' => $dto->status === 'published' ? now() : null
            ]);

            // Sync tags to relational table
            if (is_array($dto->tags) && count($dto->tags) > 0) {
                $tagIds = $this->upsertTagsAndGetIds($dto->tags);
                $knowledge->tagsRelation()->sync($tagIds);
            }

            // Handle attachments if present
            if (request()->hasFile('attachments')) {
                $files = request()->file('attachments');
                foreach ((array) $files as $file) {
                    if (!$file->isValid())
                        continue;
                    $stored = $file->store('knowledge/attachments', 'public');
                    KnowledgeAttachment::create([
                        'knowledge_id' => $knowledge->id,
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $stored,
                        'mime_type' => $file->getClientMimeType(),
                        'size_bytes' => $file->getSize(),
                        'disk' => 'public',
                    ]);
                }
            }

            return [
                'success' => true,
                'message' => 'Knowledge berhasil dibuat',
                'data' => $knowledge
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal membuat knowledge: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update knowledge
     */
    public function updateKnowledge($id, $dto)
    {
        try {
            $knowledge = Knowledge::find($id);

            if (!$knowledge) {
                return [
                    'success' => false,
                    'message' => 'Knowledge tidak ditemukan'
                ];
            }

            // Restrict SKPD access for non-admin
            $user = Auth::user();
            if ($user instanceof User) {
                if ($user->hasRole('User SKPD')) {
                    if ((int) $knowledge->skpd_id !== (int) $user->skpd_id) {
                        return [
                            'success' => false,
                            'message' => 'Anda tidak memiliki izin untuk mengubah data ini'
                        ];
                    }
                    // Prevent SKPD user from changing skpd_id
                    $dto->skpd_id = $knowledge->skpd_id;
                }
            }

            $knowledge->update([
                'title' => $dto->title,
                'content' => $dto->content,
                'description' => $dto->description,
                'tags' => $dto->tags,
                'status' => $dto->status,
                'category_id' => $dto->category_id,
                'skpd_id' => $dto->skpd_id,
                'published_at' => $dto->status === 'published' ? now() : null
            ]);

            // Sync tags to relational table
            $tagIds = is_array($dto->tags) ? $this->upsertTagsAndGetIds($dto->tags) : [];
            $knowledge->tagsRelation()->sync($tagIds);

            // Remove attachments if requested
            $removeIds = (array) request()->input('remove_attachment_ids', []);
            if (!empty($removeIds)) {
                $attachments = KnowledgeAttachment::whereIn('id', $removeIds)->where('knowledge_id', $knowledge->id)->get();
                foreach ($attachments as $att) {
                    if ($att->disk && $att->path && Storage::disk($att->disk)->exists($att->path)) {
                        Storage::disk($att->disk)->delete($att->path);
                    }
                    $att->delete();
                }
            }

            // Handle new attachments if present
            if (request()->hasFile('attachments')) {
                $files = request()->file('attachments');
                foreach ((array) $files as $file) {
                    if (!$file->isValid())
                        continue;
                    $stored = $file->store('knowledge/attachments', 'public');
                    KnowledgeAttachment::create([
                        'knowledge_id' => $knowledge->id,
                        'original_name' => $file->getClientOriginalName(),
                        'path' => $stored,
                        'mime_type' => $file->getClientMimeType(),
                        'size_bytes' => $file->getSize(),
                        'disk' => 'public',
                    ]);
                }
            }

            return [
                'success' => true,
                'message' => 'Knowledge berhasil diperbarui',
                'data' => $knowledge
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal memperbarui knowledge: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete knowledge
     */
    public function deleteKnowledge($id)
    {
        try {
            $knowledge = Knowledge::find($id);

            if (!$knowledge) {
                return [
                    'success' => false,
                    'message' => 'Knowledge tidak ditemukan'
                ];
            }

            // Restrict SKPD access for non-admin
            $user = Auth::user();
            if ($user instanceof User) {
                if ($user->hasRole('User SKPD')) {
                    if ((int) $knowledge->skpd_id !== (int) $user->skpd_id) {
                        return [
                            'success' => false,
                            'message' => 'Anda tidak memiliki izin untuk menghapus data ini'
                        ];
                    }
                }
            }

            $knowledge->delete();

            return [
                'success' => true,
                'message' => 'Knowledge berhasil dihapus'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menghapus knowledge: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Search knowledge
     */
    public function searchKnowledge($query)
    {
        try {
            $q = Knowledge::with(['category', 'author', 'skpd'])
                ->where(function ($w) use ($query) {
                    $w->where('title', 'like', '%' . $query . '%')
                        ->orWhere('description', 'like', '%' . $query . '%')
                        ->orWhere('content', 'like', '%' . $query . '%');
                });

            // Restrict SKPD for non-admin
            $user = Auth::user();
            if ($user instanceof User) {
                if ($user->hasRole('User SKPD') && $user->skpd_id) {
                    $q->where('skpd_id', $user->skpd_id);
                }
            }

            $results = $q->get();

            return [
                'success' => true,
                'data' => $results,
                'message' => 'Pencarian berhasil'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal melakukan pencarian: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Change knowledge status
     */
    public function changeStatus($id, $status)
    {
        try {
            $knowledge = Knowledge::find($id);

            if (!$knowledge) {
                return [
                    'success' => false,
                    'message' => 'Knowledge tidak ditemukan'
                ];
            }

            $knowledge->update([
                'status' => $status,
                'published_at' => $status === 'published' ? now() : null
            ]);

            return [
                'success' => true,
                'message' => 'Status knowledge berhasil diubah',
                'data' => $knowledge
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal mengubah status: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Export knowledge data
     */
    public function exportKnowledge($filters = [])
    {
        try {
            $query = Knowledge::with(['category', 'author', 'skpd']);

            // Apply filters
            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('title', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('description', 'like', '%' . $filters['search'] . '%')
                        ->orWhere('content', 'like', '%' . $filters['search'] . '%');
                });
            }

            if (!empty($filters['category_id'])) {
                $query->where('category_id', $filters['category_id']);
            }

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (!empty($filters['skpd_id'])) {
                $query->where('skpd_id', $filters['skpd_id']);
            }

            $data = $query->get();

            return [
                'success' => true,
                'data' => $data,
                'message' => 'Export berhasil'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal melakukan export: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify knowledge (approve/reject) by admin
     */
    public function verifyKnowledge(int $id, string $action, ?string $note = null)
    {
        try {
            $knowledge = Knowledge::find($id);
            if (!$knowledge) {
                return ['success' => false, 'message' => 'Knowledge tidak ditemukan'];
            }

            if (!in_array($action, ['approve', 'reject'], true)) {
                return ['success' => false, 'message' => 'Aksi verifikasi tidak valid'];
            }

            $updates = [
                'verification_status' => $action === 'approve' ? 'approved' : 'rejected',
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'verification_note' => $note,
            ];
            $knowledge->update($updates);

            return ['success' => true, 'message' => 'Verifikasi berhasil disimpan', 'data' => $knowledge];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal verifikasi: ' . $e->getMessage()];
        }
    }

    /**
     * Batalkan verifikasi: set kembali ke pending dan kosongkan metadata verifikasi
     */
    public function unverifyKnowledge(int $id, ?string $note = null): array
    {
        try {
            $knowledge = Knowledge::find($id);
            if (!$knowledge) {
                return ['success' => false, 'message' => 'Knowledge tidak ditemukan'];
            }

            $knowledge->update([
                'verification_status' => 'pending',
                'verified_by' => null,
                'verified_at' => null,
                'verification_note' => $note,
            ]);

            return ['success' => true, 'message' => 'Verifikasi dibatalkan (kembali ke pending)', 'data' => $knowledge];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Gagal membatalkan verifikasi: ' . $e->getMessage()];
        }
    }

    /**
     * Create or fetch tags and return their IDs
     */
    private function upsertTagsAndGetIds(array $tags): array
    {
        $ids = [];
        foreach ($tags as $raw) {
            $name = trim((string) $raw);
            if ($name === '') {
                continue;
            }
            $slug = Str::slug($name);
            $tag = Tag::firstOrCreate(['slug' => $slug], ['name' => $name, 'is_active' => true]);
            // Ensure master tag exists as reference
            MasterTag::firstOrCreate(['slug' => $slug], ['name' => $name, 'is_active' => true]);
            $ids[] = $tag->id;
        }
        return array_values(array_unique($ids));
    }

    /**
     * Create category if not exists
     */
    public function createCategory(string $name): array
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            return ['success' => false, 'message' => 'Nama kategori wajib diisi'];
        }

        // Basic normalization: title case
        $normalized = ucwords(mb_strtolower($trimmed));
        $category = Category::firstOrCreate(['name' => $normalized], [
            'is_active' => true,
            'color' => '#3B82F6',
        ]);

        return [
            'success' => true,
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
            ],
            'message' => 'Kategori siap digunakan',
        ];
    }
}
