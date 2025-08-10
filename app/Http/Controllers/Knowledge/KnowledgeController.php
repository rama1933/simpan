<?php

namespace App\Http\Controllers\Knowledge;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Knowledge\StoreKnowledgeRequest;
use App\Http\Requests\Knowledge\UpdateKnowledgeRequest;
use App\Services\Knowledge\KnowledgeService;
use App\Data\Knowledge\KnowledgeDTO;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KnowledgeController extends BaseController
{
    protected $knowledgeService;

    public function __construct(KnowledgeService $knowledgeService)
    {
        $this->knowledgeService = $knowledgeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'category', 'status', 'author_id', 'tags']);
        $perPage = $request->get('per_page', 15);

        $result = $this->knowledgeService->getAllKnowledge($filters, $perPage);

        if (!$result['success']) {
            return $this->renderPageWithError('Knowledge/Index', [
                'knowledge' => [],
                'filters' => $filters,
                'perPage' => $perPage
            ], $result['message']);
        }

        return $this->renderPage('Knowledge/Index', [
            'knowledge' => $result['data'],
            'filters' => $filters,
            'perPage' => $perPage
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return $this->renderPage('Knowledge/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKnowledgeRequest $request)
    {
        $dto = KnowledgeDTO::fromArray($request->validated());
        $result = $this->knowledgeService->createKnowledge($dto);

        if (!$result['success']) {
            return $this->backWithError($result['message']);
        }

        return $this->redirectWithSuccess('knowledge.index', $result['message']);
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

        return $this->renderPage('Knowledge/Show', [
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
            return $this->renderPageWithError('Knowledge/Edit', [
                'knowledge' => null
            ], $result['message']);
        }

        return $this->renderPage('Knowledge/Edit', [
            'knowledge' => $result['data']
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
            return $this->backWithError($result['message']);
        }

        return $this->redirectWithSuccess('knowledge.show', $result['message'], ['knowledge' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $result = $this->knowledgeService->deleteKnowledge($id);

        if (!$result['success']) {
            return $this->backWithError($result['message']);
        }

        return $this->redirectWithSuccess('knowledge.index', $result['message']);
    }

    /**
     * Search knowledge
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return $this->jsonError('Query pencarian tidak boleh kosong');
        }

        $result = $this->knowledgeService->searchKnowledge($query);

        if (!$result['success']) {
            return $this->jsonError($result['message']);
        }

        return $this->jsonSuccess($result['data'], $result['message']);
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
            return $this->jsonError($result['message'], $result['code']);
        }

        return $this->jsonSuccess($result['data'], $result['message']);
    }

    /**
     * Get knowledge statistics
     */
    public function statistics()
    {
        $result = $this->knowledgeService->getKnowledgeStatistics();

        if (!$result['success']) {
            return $this->jsonError($result['message']);
        }

        return $this->jsonSuccess($result['data'], $result['message']);
    }
}

