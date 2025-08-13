<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Knowledge;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class KnowledgeSearchController extends Controller
{
    /**
     * Get search suggestions based on query
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'suggestions' => [],
                'recommendations' => $this->getRecommendations()
            ]);
        }

        // Search in published and approved knowledge
        $suggestions = Knowledge::where('status', 'published')
            ->where('verification_status', 'approved')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            })
            ->with(['category', 'author', 'skpd'])
            ->select('id', 'title', 'description', 'category_id', 'author_id', 'skpd_id')
            ->limit(8)
            ->get()
            ->map(function ($knowledge) {
                return [
                    'id' => $knowledge->id,
                    'title' => $knowledge->title,
                    'description' => $knowledge->description ? substr($knowledge->description, 0, 100) . '...' : 'Tidak ada deskripsi',
                    'category' => $knowledge->category->name ?? 'Umum',
                    'author' => $knowledge->author->name ?? 'Unknown',
                    'url' => "/knowledge/public/{$knowledge->id}"
                ];
            });

        return response()->json([
            'suggestions' => $suggestions,
            'recommendations' => $this->getRecommendations(5)
        ]);
    }

    /**
     * Get knowledge recommendations
     */
    public function recommendations(): JsonResponse
    {
        return response()->json([
            'recommendations' => $this->getRecommendations()
        ]);
    }

    /**
     * Get popular/recommended knowledge
     */
    private function getRecommendations(int $limit = 8): array
    {
        // Get popular knowledge based on different criteria
        $recommendations = collect();

        // Recent popular knowledge
        $recent = Knowledge::where('status', 'published')
            ->where('verification_status', 'approved')
            ->with(['category', 'author', 'skpd'])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Knowledge from different categories
        $categories = Category::withCount('knowledge')
            ->orderBy('knowledge_count', 'desc')
            ->limit(5)
            ->get();

        foreach ($categories as $category) {
            $categoryKnowledge = Knowledge::where('status', 'published')
                ->where('verification_status', 'approved')
                ->where('category_id', $category->id)
                ->with(['category', 'author', 'skpd'])
                ->inRandomOrder()
                ->first();

            if ($categoryKnowledge) {
                $recommendations->push($categoryKnowledge);
            }
        }

        // Add recent knowledge
        $recommendations = $recommendations->merge($recent);

        // Remove duplicates and limit
        $recommendations = $recommendations->unique('id')->take($limit);

        return $recommendations->map(function ($knowledge) {
            return [
                'id' => $knowledge->id,
                'title' => $knowledge->title,
                'description' => $knowledge->description ? substr($knowledge->description, 0, 120) . '...' : 'Tidak ada deskripsi',
                'category' => $knowledge->category->name ?? 'Umum',
                'author' => $knowledge->author->name ?? 'Unknown',
                'skpd' => $knowledge->skpd->nama_skpd ?? 'SKPD',
                'created_at' => $knowledge->created_at->format('d M Y'),
                'url' => "/knowledge/public/{$knowledge->id}"
            ];
        })->values()->toArray();
    }

    /**
     * Advanced search with filters
     */
    public function advancedSearch(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category_id');
        $skpdId = $request->get('skpd_id');
        $tags = $request->get('tags', []);
        $sortBy = $request->get('sort_by', 'relevance');
        $perPage = min($request->get('per_page', 15), 50);

        $knowledgeQuery = Knowledge::where('status', 'published')
            ->where('verification_status', 'approved')
            ->with(['category', 'author', 'skpd', 'tagsRelation']);

        // Apply search filter
        if (!empty($query)) {
            $knowledgeQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('content', 'like', "%{$query}%");
            });
        }

        // Apply category filter
        if ($categoryId) {
            $knowledgeQuery->where('category_id', $categoryId);
        }

        // Apply SKPD filter
        if ($skpdId) {
            $knowledgeQuery->where('skpd_id', $skpdId);
        }

        // Apply tags filter
        if (!empty($tags) && is_array($tags)) {
            $knowledgeQuery->whereHas('tagsRelation', function ($q) use ($tags) {
                $q->whereIn('tags.id', $tags);
            });
        }

        // Apply sorting
        switch ($sortBy) {
            case 'newest':
                $knowledgeQuery->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $knowledgeQuery->orderBy('created_at', 'asc');
                break;
            case 'title':
                $knowledgeQuery->orderBy('title', 'asc');
                break;
            case 'relevance':
            default:
                if (!empty($query)) {
                    // Simple relevance scoring
                    $knowledgeQuery->selectRaw('
                        knowledge.*,
                        (
                            CASE WHEN title LIKE ? THEN 3 ELSE 0 END +
                            CASE WHEN description LIKE ? THEN 2 ELSE 0 END +
                            CASE WHEN content LIKE ? THEN 1 ELSE 0 END
                        ) as relevance_score
                    ', ["%{$query}%", "%{$query}%", "%{$query}%"])
                    ->orderBy('relevance_score', 'desc')
                    ->orderBy('created_at', 'desc');
                } else {
                    $knowledgeQuery->orderBy('created_at', 'desc');
                }
                break;
        }

        $results = $knowledgeQuery->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $results->items(),
            'pagination' => [
                'current_page' => $results->currentPage(),
                'last_page' => $results->lastPage(),
                'per_page' => $results->perPage(),
                'total' => $results->total(),
                'from' => $results->firstItem(),
                'to' => $results->lastItem()
            ],
            'query' => $query,
            'filters' => [
                'category_id' => $categoryId,
                'skpd_id' => $skpdId,
                'tags' => $tags,
                'sort_by' => $sortBy
            ]
        ]);
    }

    /**
     * Get trending searches or popular keywords
     */
    public function trendingSearches(): JsonResponse
    {
        // This could be enhanced with actual search analytics
        $trending = [
            'Teknologi Digital',
            'Manajemen Keuangan',
            'Pelayanan Publik',
            'Inovasi Daerah',
            'Tata Kelola Pemerintahan',
            'Pembangunan Berkelanjutan',
            'Digitalisasi',
            'Smart City'
        ];

        return response()->json([
            'trending' => $trending
        ]);
    }
}