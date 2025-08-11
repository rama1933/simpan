<?php

namespace App\Http\Controllers\Knowledge;

use App\Http\Controllers\Controller;
use App\Services\Knowledge\KnowledgeService;
use App\Http\Requests\Knowledge\StoreKnowledgeRequest;
use App\Http\Requests\Knowledge\UpdateKnowledgeRequest;
use App\Data\Knowledge\KnowledgeDTO;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Tag;
use App\Models\MasterTag;

class KnowledgeController extends Controller
{
    public function __construct(private KnowledgeService $knowledgeService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'category_id', 'skpd_id', 'status']);
        $knowledge = $this->knowledgeService->getAllKnowledge($filters);

        // Get categories and SKPDs for filters
        $categories = $this->knowledgeService->getCategories();
        $skpds = $this->knowledgeService->getSKPDs();

        return Inertia::render('Knowledge/Index', [
            'knowledge' => $knowledge['data'],
            'filters' => $filters,
            'categories' => $categories,
            'skpds' => $skpds,
            'user' => auth()->user()
        ]);
    }

    /**
     * Filter knowledge data
     */
    public function filter(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'skpd_id', 'status']);
        $knowledge = $this->knowledgeService->getAllKnowledge($filters);

        // Return JSON response for API
        return response()->json([
            'success' => true,
            'knowledge' => [
                'success' => true,
                'data' => $knowledge['data']
            ],
            'filters' => $filters
        ]);
    }

    /**
     * Quick create category from form (AJAX)
     */
    public function quickCreateCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $result = $this->knowledgeService->createCategory($request->input('name'));

        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data']
        ]);
    }

    /**
     * List available tags (optional search)
     */
    public function tags(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $query = MasterTag::query()->where('is_active', true);
        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }
        $tags = $query->orderBy('name')->limit(50)->get(['id', 'name', 'slug']);
        return response()->json(['success' => true, 'data' => $tags]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $categories = $this->knowledgeService->getCategories();
        $skpds = $this->knowledgeService->getSKPDs();

        return Inertia::render('Knowledge/Create', [
            'categories' => $categories,
            'skpds' => $skpds,
            'user' => auth()->user()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKnowledgeRequest $request)
    {
        $dto = KnowledgeDTO::fromArray($request->validated());
        $result = $this->knowledgeService->createKnowledge($dto);

        if (!$result['success']) {
            return back()->withErrors($result['message'])->withInput();
        }

        return redirect()->route('knowledge.index')->with('success', 'Pengetahuan berhasil disimpan');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id): Response
    {
        $result = $this->knowledgeService->getKnowledgeById($id);

        if (!$result['success']) {
            return $this->renderPageWithError('Knowledge/Show', [
                'knowledge' => null
            ], $result['message']);
        }

        return Inertia::render('Knowledge/Show', [
            'knowledge' => $result['data']
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id): Response
    {
        $result = $this->knowledgeService->getKnowledgeById($id);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        $categories = $this->knowledgeService->getCategories();
        $skpds = $this->knowledgeService->getSKPDs();

        return Inertia::render('Knowledge/Edit', [
            'knowledge' => $result['data'],
            'categories' => $categories,
            'skpds' => $skpds
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKnowledgeRequest $request, int $id)
    {
        $dto = KnowledgeDTO::fromArray($request->validated());
        $result = $this->knowledgeService->updateKnowledge($id, $dto);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('knowledge.show', $id)->with('success', $result['message']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $result = $this->knowledgeService->deleteKnowledge($id);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        return redirect()->route('knowledge.index')->with('success', $result['message']);
    }

    /**
     * Verify knowledge (Admin only)
     */
    public function verify(Request $request, int $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:500'
        ]);

        $result = $this->knowledgeService->verifyKnowledge($id, $request->action, $request->note);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Search knowledge
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return back()->withErrors(['error' => 'Query pencarian tidak boleh kosong']);
        }

        $result = $this->knowledgeService->searchKnowledge($query);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        return Inertia::render('Knowledge/Search', [
            'results' => $result['data'],
            'query' => $query
        ]);
    }

    /**
     * Change knowledge status (admin only)
     */
    public function changeStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:draft,published,archived'
        ]);

        $result = $this->knowledgeService->changeStatus($id, $request->status);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        return back()->with('success', $result['message']);
    }

    /**
     * Export knowledge data
     */
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'skpd_id', 'status']);
        $result = $this->knowledgeService->exportKnowledge($filters);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        return $result['data'];
    }

    /**
     * Get knowledge statistics
     */
    public function statistics(): Response
    {
        $stats = $this->knowledgeService->getKnowledgeStatistics();
        $categoryDistribution = $this->knowledgeService->getCategoryDistribution();
        $statusDistribution = $this->knowledgeService->getStatusDistribution();
        $authorStatistics = $this->knowledgeService->getAuthorStatistics();
        $monthlyTrends = $this->knowledgeService->getMonthlyTrends();
        $recentActivities = $this->knowledgeService->getRecentActivities();

        return Inertia::render('Knowledge/Statistics', [
            'stats' => $stats,
            'categoryDistribution' => $categoryDistribution,
            'statusDistribution' => $statusDistribution,
            'authorStatistics' => $authorStatistics,
            'monthlyTrends' => $monthlyTrends,
            'recentActivities' => $recentActivities
        ]);
    }
}

