<?php

namespace App\Observers;

use App\Models\Knowledge;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class KnowledgeObserver extends BaseObserver
{
    /**
     * Handle the Knowledge "created" event.
     */
    protected function onCreated(Model $model): void
    {
        if ($model instanceof Knowledge) {
            $this->clearCache();
            $this->logKnowledgeActivity('created', $model);
        }
    }

    /**
     * Handle the Knowledge "updated" event.
     */
    protected function onUpdated(Model $model): void
    {
        if ($model instanceof Knowledge) {
            $this->clearCache();
            $this->logKnowledgeActivity('updated', $model);
        }
    }

    /**
     * Handle the Knowledge "deleted" event.
     */
    protected function onDeleted(Model $model): void
    {
        if ($model instanceof Knowledge) {
            $this->clearCache();
            $this->logKnowledgeActivity('deleted', $model);
        }
    }

    /**
     * Handle the Knowledge "restored" event.
     */
    protected function onRestored(Model $model): void
    {
        if ($model instanceof Knowledge) {
            $this->clearCache();
            $this->logKnowledgeActivity('restored', $model);
        }
    }

    /**
     * Handle the Knowledge "force deleted" event.
     */
    protected function onForceDeleted(Model $model): void
    {
        if ($model instanceof Knowledge) {
            $this->clearCache();
            $this->logKnowledgeActivity('force deleted', $model);
        }
    }

    /**
     * Clear knowledge related cache
     */
    private function clearCache(): void
    {
        Cache::forget('knowledge_statistics');
        Cache::forget('knowledge_categories');
        Cache::forget('knowledge_recent');
    }

    /**
     * Log knowledge specific activity
     */
    private function logKnowledgeActivity(string $action, Knowledge $knowledge): void
    {
        Log::info("Knowledge {$action}", [
            'knowledge_id' => $knowledge->id,
            'title' => $knowledge->title,
            'status' => $knowledge->status,
            'category' => $knowledge->category,
            'author_id' => $knowledge->author_id,
            'action' => $action,
            'timestamp' => now()->toISOString()
        ]);
    }
}
