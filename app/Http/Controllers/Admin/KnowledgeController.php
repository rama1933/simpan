<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Services\Knowledge\KnowledgeService;
use App\Http\Requests\Knowledge\StoreKnowledgeRequest;
use App\Http\Requests\Knowledge\UpdateKnowledgeRequest;
use App\Data\Knowledge\KnowledgeDTO;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class KnowledgeController extends BaseController
{
    public function __construct(private KnowledgeService $knowledgeService)
    {
    }

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'category_id', 'skpd_id', 'status', 'verification_status', 'tags']);
        $knowledge = $this->knowledgeService->getAllKnowledge($filters);
        $categories = $this->knowledgeService->getCategories();
        $skpds = $this->knowledgeService->getSKPDs();

        $user = Auth::user();
        $userWithRoles = \App\Models\User::with('roles')->find($user->id);
        
        return Inertia::render('Admin/Knowledge/Index', [
            'knowledge' => $knowledge['data'],
            'filters' => $filters,
            'categories' => $categories,
            'skpds' => $skpds,
            'user' => $userWithRoles
        ]);
    }

    public function filter(Request $request)
    {
        $filters = $request->only(['search', 'category_id', 'skpd_id', 'status', 'verification_status', 'tags']);
        $knowledge = $this->knowledgeService->getAllKnowledge($filters);

        return response()->json([
            'success' => true,
            'knowledge' => [
                'success' => true,
                'data' => $knowledge['data']
            ],
            'filters' => $filters
        ]);
    }

    public function quickCreateCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $result = $this->knowledgeService->createCategory($request->input('name'));
        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }
        return response()->json(['success' => true, 'data' => $result['data']]);
    }

    public function tags(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $query = \App\Models\MasterTag::query()->where('is_active', true);
        if ($q !== '') {
            $query->where('name', 'like', "%{$q}%");
        }
        $tags = $query->orderBy('name')->limit(50)->get(['id', 'name', 'slug']);
        return response()->json(['success' => true, 'data' => $tags]);
    }

    public function create(): Response
    {
        $categories = $this->knowledgeService->getCategories();
        $skpds = $this->knowledgeService->getSKPDs();

        return Inertia::render('Admin/Knowledge/Create', [
            'categories' => $categories,
            'skpds' => $skpds,
            'user' => Auth::user()
        ]);
    }

    public function store(StoreKnowledgeRequest $request)
    {
        $dto = KnowledgeDTO::fromArray($request->validated());
        $result = $this->knowledgeService->createKnowledge($dto);
        if (!$result['success']) {
            return back()->withErrors($result['message'])->withInput();
        }
        return redirect()->route('admin.knowledge.index')->with('success', 'Pengetahuan berhasil disimpan');
    }

    public function show(int $id): Response
    {
        $result = $this->knowledgeService->getKnowledgeById($id);
        if (!$result['success']) {
            return $this->renderPageWithError('Knowledge/Show', ['knowledge' => null], $result['message']);
        }
        return Inertia::render('Admin/Knowledge/Show', [
            'knowledge' => $result['data'],
            'user' => Auth::user(),
            'canVerify' => true,
        ]);
    }

    public function edit(int $id)
    {
        $result = $this->knowledgeService->getKnowledgeById($id);
        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }
        $categories = $this->knowledgeService->getCategories();
        $skpds = $this->knowledgeService->getSKPDs();
        return Inertia::render('Admin/Knowledge/Edit', [
            'knowledge' => $result['data'],
            'categories' => $categories,
            'skpds' => $skpds
        ]);
    }

    public function update(UpdateKnowledgeRequest $request, int $id)
    {
        $dto = KnowledgeDTO::fromArray($request->validated());
        $result = $this->knowledgeService->updateKnowledge($id, $dto);
        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }
        return redirect()->route('admin.knowledge.show', $id)->with('success', $result['message']);
    }

    public function destroy(int $id)
    {
        $result = $this->knowledgeService->deleteKnowledge($id);
        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }
        return redirect()->route('admin.knowledge.index')->with('success', $result['message']);
    }

    public function verify(Request $request, int $id)
    {
        $request->validate(['action' => 'required|in:approve,reject', 'note' => 'nullable|string|max:500']);
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
        return redirect()->route('admin.knowledge.show', $id)->with('success', $result['message']);
    }

    public function unverify(Request $request, int $id)
    {
        $request->validate(['note' => 'nullable|string|max:500']);
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
        return redirect()->route('admin.knowledge.show', $id)->with('success', $result['message']);
    }

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

    public function changeStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:draft,published,archived']);
        $result = $this->knowledgeService->changeStatus($id, $request->status);
        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }
        return back()->with('success', $result['message']);
    }

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