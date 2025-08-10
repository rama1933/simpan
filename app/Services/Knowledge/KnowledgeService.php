<?php

namespace App\Services\Knowledge;

use App\Models\Knowledge;
use App\Models\Category;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        if (!empty($filters['category'])) {
            $query->whereHas('category', function ($q) use ($filters) {
                $q->where('name', $filters['category']);
            });
        }

        // Apply status filter
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        // Apply SKPD filter
        if (!empty($filters['skpd'])) {
            $query->where('skpd_id', $filters['skpd']);
        }

        return [
            'success' => true,
            'data' => $query->paginate($perPage)
        ];
    }
}
