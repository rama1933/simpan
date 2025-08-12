<?php

namespace App\Services\ChangeLog;

use App\Models\ChangeLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ChangeLogService
{
    /**
     * Log a change to an entity
     */
    public function logChange(
        Model $entity,
        string $action,
        ?string $fieldName = null,
        $oldValue = null,
        $newValue = null,
        ?string $description = null,
        ?array $metadata = null
    ): ChangeLog {
        return ChangeLog::create([
            'entity_type' => get_class($entity),
            'entity_id' => $entity->getKey(),
            'action' => $action,
            'field_name' => $fieldName,
            'old_value' => $this->serializeValue($oldValue),
            'new_value' => $this->serializeValue($newValue),
            'description' => $description,
            'metadata' => $metadata,
            'user_id' => Auth::id(),
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'changed_at' => now()
        ]);
    }

    /**
     * Log multiple field changes at once
     */
    public function logMultipleChanges(
        Model $entity,
        string $action,
        array $changes,
        ?string $description = null,
        ?array $metadata = null
    ): Collection {
        $logs = collect();

        foreach ($changes as $fieldName => $values) {
            $logs->push($this->logChange(
                $entity,
                $action,
                $fieldName,
                $values['old'] ?? null,
                $values['new'] ?? null,
                $description,
                $metadata
            ));
        }

        return $logs;
    }

    /**
     * Get change logs for a specific entity
     */
    public function getEntityChanges(
        string $entityType,
        int $entityId,
        int $perPage = 15
    ): LengthAwarePaginator {
        return ChangeLog::with('user')
            ->forEntity($entityType, $entityId)
            ->orderBy('changed_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get recent changes across all entities
     */
    public function getRecentChanges(
        int $limit = 50,
        ?string $entityType = null,
        ?string $action = null
    ): Collection {
        $query = ChangeLog::with('user')
            ->orderBy('changed_at', 'desc')
            ->limit($limit);

        if ($entityType) {
            $query->forEntity($entityType);
        }

        if ($action) {
            $query->byAction($action);
        }

        return $query->get()->map(function ($log) {
            return $log->change_summary;
        });
    }

    /**
     * Get changes by user
     */
    public function getUserChanges(
        int $userId,
        int $perPage = 15,
        ?string $entityType = null
    ): LengthAwarePaginator {
        $query = ChangeLog::with('user')
            ->byUser($userId)
            ->orderBy('changed_at', 'desc');

        if ($entityType) {
            $query->forEntity($entityType);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get changes in date range
     */
    public function getChangesInDateRange(
        Carbon $startDate,
        Carbon $endDate,
        ?string $entityType = null,
        ?string $action = null
    ): Collection {
        $query = ChangeLog::with('user')
            ->inDateRange($startDate, $endDate)
            ->orderBy('changed_at', 'desc');

        if ($entityType) {
            $query->forEntity($entityType);
        }

        if ($action) {
            $query->byAction($action);
        }

        return $query->get();
    }

    /**
     * Get change statistics
     */
    public function getChangeStatistics(?string $entityType = null): array
    {
        $query = ChangeLog::query();

        if ($entityType) {
            $query->forEntity($entityType);
        }

        $totalChanges = $query->count();
        $todayChanges = $query->whereDate('changed_at', today())->count();
        $weekChanges = $query->whereBetween('changed_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $monthChanges = $query->whereMonth('changed_at', now()->month)->count();

        $actionStats = $query->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action')
            ->toArray();

        $userStats = $query->selectRaw('user_id, COUNT(*) as count')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->with('user')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($stat) {
                return [
                    'user_name' => $stat->user?->name ?? 'Unknown',
                    'count' => $stat->count
                ];
            });

        return [
            'total_changes' => $totalChanges,
            'today_changes' => $todayChanges,
            'week_changes' => $weekChanges,
            'month_changes' => $monthChanges,
            'action_distribution' => $actionStats,
            'top_users' => $userStats
        ];
    }

    /**
     * Get daily change trends for the last 30 days
     */
    public function getDailyTrends(?string $entityType = null): Collection
    {
        $query = ChangeLog::selectRaw('DATE(changed_at) as date, COUNT(*) as count')
            ->whereBetween('changed_at', [now()->subDays(30), now()])
            ->groupBy('date')
            ->orderBy('date');

        if ($entityType) {
            $query->forEntity($entityType);
        }

        return $query->get();
    }

    /**
     * Serialize value for storage
     */
    private function serializeValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return (string) $value;
    }

    /**
     * Compare two models and return the differences
     */
    public function compareModels(Model $oldModel, Model $newModel): array
    {
        $changes = [];
        $oldAttributes = $oldModel->getAttributes();
        $newAttributes = $newModel->getAttributes();

        foreach ($newAttributes as $key => $newValue) {
            $oldValue = $oldAttributes[$key] ?? null;
            
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }

        return $changes;
    }

    /**
     * Clean old change logs (older than specified days)
     */
    public function cleanOldLogs(int $daysToKeep = 365): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        return ChangeLog::where('changed_at', '<', $cutoffDate)->delete();
    }
}