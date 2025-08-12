<?php

namespace App\Http\Controllers\Knowledge;

use App\Http\Controllers\BaseController;
use App\Services\Knowledge\KnowledgeService;
use App\Http\Requests\Knowledge\StoreKnowledgeRequest;
use App\Http\Requests\Knowledge\UpdateKnowledgeRequest;
use App\Data\Knowledge\KnowledgeDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Tag;
use App\Models\MasterTag;
use App\Models\User;

class KnowledgeController extends BaseController
{
    public function __construct(private KnowledgeService $knowledgeService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'category_id', 'skpd_id', 'status', 'verification_status', 'tags']);
        $knowledge = $this->knowledgeService->getAllKnowledge($filters);

        // Get categories and SKPDs for filters
        $categories = $this->knowledgeService->getCategories();
        $skpds = $this->knowledgeService->getSKPDs();

        return Inertia::render('Knowledge/Index', [
            'knowledge' => $knowledge['data'],
            'filters' => $filters,
            'categories' => $categories,
            'skpds' => $skpds,
            'user' => Auth::user()
        ]);
    }

    /**
     * Filter knowledge data
     */
    public function filter(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'skpd_id', 'status', 'verification_status', 'tags']);
        
        // If user is SKPD, force their skpd_id filter
        $user = Auth::user();
        if ($user instanceof User) {
            if ($user->hasRole('User SKPD') && $user->skpd_id) {
                $filters['skpd_id'] = $user->skpd_id;
            }
        }
        
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
            'user' => Auth::user()
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

        $user = Auth::user();
        $canVerify = false;
        if ($user instanceof User) {
            $canVerify = $user->hasRole('Admin');
        }
        return Inertia::render('Knowledge/Show', [
            'knowledge' => $result['data'],
            'user' => $user,
            'canVerify' => $canVerify
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
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
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $result['message']], 422);
            }
            return back()->withErrors(['error' => $result['message']]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $result['message'], 'data' => $result['data']]);
        }

        return redirect()->route('knowledge.show', $id)->with('success', $result['message']);
    }

    /**
     * Unverify knowledge (Admin only)
     */
    public function unverify(Request $request, int $id)
    {
        $request->validate([
            'note' => 'nullable|string|max:500'
        ]);

        $result = $this->knowledgeService->unverifyKnowledge($id, $request->note);

        if (!$result['success']) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $result['message']], 422);
            }
            return back()->withErrors(['error' => $result['message']]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $result['message'], 'data' => $result['data']]);
        }

        return redirect()->route('knowledge.show', $id)->with('success', $result['message']);
    }

    /**
     * Search knowledge
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
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
     * Change status
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
     * Export knowledge
     */
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'status', 'skpd_id']);
        $result = $this->knowledgeService->exportKnowledge($filters);

        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }

        return Inertia::render('Knowledge/Export', [
            'data' => $result['data']
        ]);
    }

    /**
     * Statistics dashboard
     */
    public function statistics()
    {
        $stats = $this->knowledgeService->getKnowledgeStatistics();
        $categoryDistribution = $this->knowledgeService->getCategoryDistribution();
        $statusDistribution = $this->knowledgeService->getStatusDistribution();
        $authorStats = $this->knowledgeService->getAuthorStatistics();
        $monthlyTrends = $this->knowledgeService->getMonthlyTrends();
        $recentActivities = $this->knowledgeService->getRecentActivities(10);

        return Inertia::render('Knowledge/Statistics', [
            'stats' => $stats,
            'categoryDistribution' => $categoryDistribution,
            'statusDistribution' => $statusDistribution,
            'authorStats' => $authorStats,
            'monthlyTrends' => $monthlyTrends,
            'recentActivities' => $recentActivities,
        ]);
    }
}

