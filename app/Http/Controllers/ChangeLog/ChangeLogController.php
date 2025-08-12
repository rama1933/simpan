<?php

namespace App\Http\Controllers\ChangeLog;

use App\Http\Controllers\Controller;
use App\Services\ChangeLog\ChangeLogService;
use App\Models\ChangeLog;
use App\Models\Knowledge;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class ChangeLogController extends Controller
{
    public function __construct(
        private ChangeLogService $changeLogService
    ) {}

    /**
     * Display change logs index page
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['entity_type', 'action', 'user_id', 'date_from', 'date_to', 'search']);
        
        $query = ChangeLog::with(['user'])
            ->orderBy('changed_at', 'desc');

        // Apply filters
        if ($filters['entity_type'] ?? null) {
            $query->forEntity($filters['entity_type']);
        }

        if ($filters['action'] ?? null) {
            $query->byAction($filters['action']);
        }

        if ($filters['user_id'] ?? null) {
            $query->byUser($filters['user_id']);
        }

        if (($filters['date_from'] ?? null) && ($filters['date_to'] ?? null)) {
            $query->inDateRange(
                Carbon::parse($filters['date_from']),
                Carbon::parse($filters['date_to'])
            );
        }

        if ($filters['search'] ?? null) {
            $query->where(function ($q) use ($filters) {
                $q->where('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('field_name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('old_value', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('new_value', 'like', '%' . $filters['search'] . '%');
            });
        }

        $changeLogs = $query->paginate(20);
        $statistics = $this->changeLogService->getChangeStatistics();

        return Inertia::render('ChangeLog/Index', [
            'changeLogs' => $changeLogs,
            'statistics' => $statistics,
            'filters' => $filters,
            'entityTypes' => $this->getEntityTypes(),
            'actions' => $this->getActions()
        ]);
    }

    /**
     * Show change logs for specific knowledge
     */
    public function showKnowledgeChanges(Request $request, Knowledge $knowledge): Response
    {
        $changeLogs = $this->changeLogService->getEntityChanges(
            Knowledge::class,
            $knowledge->id,
            20
        );

        return Inertia::render('ChangeLog/KnowledgeChanges', [
            'knowledge' => $knowledge->load(['author', 'category', 'skpd']),
            'changeLogs' => $changeLogs
        ]);
    }

    /**
     * Get change statistics API
     */
    public function getStatistics(Request $request): JsonResponse
    {
        $entityType = $request->get('entity_type');
        $statistics = $this->changeLogService->getChangeStatistics($entityType);
        
        return response()->json($statistics);
    }

    /**
     * Get daily trends API
     */
    public function getDailyTrends(Request $request): JsonResponse
    {
        $entityType = $request->get('entity_type');
        $trends = $this->changeLogService->getDailyTrends($entityType);
        
        return response()->json($trends);
    }

    /**
     * Get recent changes API
     */
    public function getRecentChanges(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 50);
        $entityType = $request->get('entity_type');
        $action = $request->get('action');
        
        $changes = $this->changeLogService->getRecentChanges($limit, $entityType, $action);
        
        return response()->json($changes);
    }

    /**
     * Get user changes API
     */
    public function getUserChanges(Request $request, int $userId): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $entityType = $request->get('entity_type');
        
        $changes = $this->changeLogService->getUserChanges($userId, $perPage, $entityType);
        
        return response()->json($changes);
    }

    /**
     * Export change logs
     */
    public function export(Request $request)
    {
        $request->validate([
            'format' => 'required|in:csv,excel,pdf',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'entity_type' => 'nullable|string',
            'action' => 'nullable|string'
        ]);

        // TODO: Implement export functionality
        // This would typically use Laravel Excel or similar package
        
        return response()->json([
            'message' => 'Export functionality will be implemented',
            'filters' => $request->all()
        ]);
    }

    /**
     * Clean old logs (admin only)
     */
    public function cleanOldLogs(Request $request): JsonResponse
    {
        $request->validate([
            'days_to_keep' => 'required|integer|min:30|max:3650'
        ]);

        $deletedCount = $this->changeLogService->cleanOldLogs($request->days_to_keep);
        
        return response()->json([
            'message' => "Berhasil menghapus {$deletedCount} log lama",
            'deleted_count' => $deletedCount
        ]);
    }

    /**
     * Get available entity types
     */
    private function getEntityTypes(): array
    {
        return [
            'App\\Models\\Knowledge' => 'Pengetahuan',
            'App\\Models\\User' => 'Pengguna',
            'App\\Models\\Category' => 'Kategori',
            'App\\Models\\MasterSKPD' => 'SKPD'
        ];
    }

    /**
     * Get available actions
     */
    private function getActions(): array
    {
        return [
            'created' => 'Dibuat',
            'updated' => 'Diperbarui',
            'deleted' => 'Dihapus',
            'published' => 'Dipublikasikan',
            'archived' => 'Diarsipkan',
            'field_updated' => 'Field Diperbarui',
            'restored' => 'Dipulihkan'
        ];
    }
}