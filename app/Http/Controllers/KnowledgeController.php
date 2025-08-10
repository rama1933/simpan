<?php

namespace App\Http\Controllers;

use App\Models\Knowledge;
use App\Models\Category;
use App\Services\Knowledge\KnowledgeService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class KnowledgeController extends Controller
{
    protected $knowledgeService;

    public function __construct(KnowledgeService $knowledgeService)
    {
        $this->knowledgeService = $knowledgeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Knowledge::with(['author', 'category'])
            ->when($request->search, function ($query, $search) {
                $query->search($search);
            })
            ->when($request->category, function ($query, $category) {
                $query->filter(['category' => $category]);
            })
            ->when($request->status, function ($query, $status) {
                $query->filter(['status' => $status]);
            })
            ->when($request->author, function ($query, $author) {
                $query->filter(['author' => $author]);
            });

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $knowledge = $query->paginate(15)->withQueryString();
        $categories = Category::all();

        return Inertia::render('Knowledge/Index', [
            'knowledge' => $knowledge,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'status', 'author', 'sort_by', 'sort_order']),
            'user' => Auth::user()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();

        return Inertia::render('Knowledge/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:published,draft,archived',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        $validated['author_id'] = Auth::id();
        $validated['tags'] = $validated['tags'] ?? [];

        $knowledge = Knowledge::create($validated);

        return redirect()->route('knowledge.show', $knowledge)
            ->with('success', 'Pengetahuan berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Knowledge $knowledge)
    {
        $knowledge->load(['author', 'category']);

        return Inertia::render('Knowledge/Show', [
            'knowledge' => $knowledge
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Knowledge $knowledge)
    {
        $categories = Category::all();

        return Inertia::render('Knowledge/Edit', [
            'knowledge' => $knowledge,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Knowledge $knowledge)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'description' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:published,draft,archived',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        $validated['tags'] = $validated['tags'] ?? [];

        $knowledge->update($validated);

        return redirect()->route('knowledge.show', $knowledge)
            ->with('success', 'Pengetahuan berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Knowledge $knowledge)
    {
        $knowledge->delete();

        return redirect()->route('knowledge.index')
            ->with('success', 'Pengetahuan berhasil dihapus!');
    }

    /**
     * Display the dashboard.
     */
    public function dashboard()
    {
        $statistics = $this->knowledgeService->getKnowledgeStatistics();
        $categoryDistribution = $this->knowledgeService->getCategoryDistribution();
        $recentActivities = $this->knowledgeService->getRecentActivities();

        return Inertia::render('Knowledge/Dashboard', [
            'statistics' => $statistics,
            'categoryDistribution' => $categoryDistribution,
            'recentActivities' => $recentActivities
        ]);
    }

    /**
     * Display search results.
     */
    public function search(Request $request)
    {
        $query = Knowledge::with(['author', 'category']);

        // Apply search filters
        if ($request->filled('query')) {
            $query->search($request->query);
        }

        if ($request->filled('category')) {
            $query->filter(['category' => $request->category]);
        }

        if ($request->filled('status')) {
            $query->filter(['status' => $request->status]);
        }

        if ($request->filled('dateFrom')) {
            $query->whereDate('created_at', '>=', $request->dateFrom);
        }

        if ($request->filled('dateTo')) {
            $query->whereDate('created_at', '<=', $request->dateTo);
        }

        if ($request->filled('author')) {
            $query->filter(['author' => $request->author]);
        }

        // Apply sorting
        $sortBy = $request->get('sortBy', 'relevance');
        switch ($sortBy) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            default:
                // relevance - keep default order
                break;
        }

        $searchResults = $query->paginate(20)->withQueryString();
        $categories = Category::all();

        return Inertia::render('Knowledge/Search', [
            'searchResults' => $searchResults,
            'categories' => $categories,
            'filters' => $request->only(['query', 'category', 'status', 'dateFrom', 'dateTo', 'author', 'sortBy'])
        ]);
    }

    /**
     * Display statistics.
     */
    public function statistics()
    {
        $statistics = $this->knowledgeService->getKnowledgeStatistics();
        $categoryDistribution = $this->knowledgeService->getCategoryDistribution();
        $statusDistribution = $this->knowledgeService->getStatusDistribution();
        $authorStatistics = $this->knowledgeService->getAuthorStatistics();
        $monthlyTrends = $this->knowledgeService->getMonthlyTrends();

        return Inertia::render('Knowledge/Statistics', [
            'statistics' => $statistics,
            'categoryDistribution' => $categoryDistribution,
            'statusDistribution' => $statusDistribution,
            'authorStatistics' => $authorStatistics,
            'monthlyTrends' => $monthlyTrends
        ]);
    }

    /**
     * Display export page.
     */
    public function export()
    {
        return Inertia::render('Knowledge/Export');
    }
}

