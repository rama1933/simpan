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
use App\Models\User;

class SkpdKnowledgeController extends BaseController
{
    public function __construct(private KnowledgeService $knowledgeService)
    {
    }

    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'category_id', 'status', 'tags']);
        // paksa filter skpd pengguna
        $user = Auth::user();
        if ($user instanceof User && $user->skpd_id) {
            $filters['skpd_id'] = $user->skpd_id;
        }
        $knowledge = $this->knowledgeService->getAllKnowledge($filters);
        $categories = $this->knowledgeService->getCategories();

        return Inertia::render('Knowledge/Index', [
            'knowledge' => $knowledge['data'],
            'filters' => $filters,
            'categories' => $categories,
            'user' => $user,
        ]);
    }

    public function create(): Response
    {
        $categories = $this->knowledgeService->getCategories();
        return Inertia::render('Knowledge/Create', [
            'categories' => $categories,
            'user' => Auth::user(),
        ]);
    }

    public function store(StoreKnowledgeRequest $request)
    {
        $dto = KnowledgeDTO::fromArray($request->validated());
        $result = $this->knowledgeService->createKnowledge($dto);
        if (!$result['success']) {
            return back()->withErrors($result['message'])->withInput();
        }
        return redirect()->route('skpd.knowledge.index')->with('success', 'Pengetahuan berhasil disimpan');
    }

    public function show(int $id): Response
    {
        $result = $this->knowledgeService->getKnowledgeById($id);
        if (!$result['success']) {
            return $this->renderPageWithError('Knowledge/Show', ['knowledge' => null], $result['message']);
        }
        return Inertia::render('Knowledge/Show', [
            'knowledge' => $result['data'],
            'user' => Auth::user(),
            'canVerify' => false,
        ]);
    }

    public function edit(int $id)
    {
        $result = $this->knowledgeService->getKnowledgeById($id);
        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }
        $categories = $this->knowledgeService->getCategories();
        return Inertia::render('Knowledge/Edit', [
            'knowledge' => $result['data'],
            'categories' => $categories,
        ]);
    }

    public function update(UpdateKnowledgeRequest $request, int $id)
    {
        $dto = KnowledgeDTO::fromArray($request->validated());
        $result = $this->knowledgeService->updateKnowledge($id, $dto);
        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }
        return redirect()->route('skpd.knowledge.show', $id)->with('success', $result['message']);
    }

    public function destroy(int $id)
    {
        $result = $this->knowledgeService->deleteKnowledge($id);
        if (!$result['success']) {
            return back()->withErrors(['error' => $result['message']]);
        }
        return redirect()->route('skpd.knowledge.index')->with('success', $result['message']);
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
}