<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

abstract class BaseObserver
{
    /**
     * Handle the Model "created" event.
     */
    public function created(Model $model): void
    {
        $this->logEvent('created', $model);
        $this->onCreated($model);
    }

    /**
     * Handle the Model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->logEvent('updated', $model);
        $this->onUpdated($model);
    }

    /**
     * Handle the Model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->logEvent('deleted', $model);
        $this->onDeleted($model);
    }

    /**
     * Handle the Model "restored" event.
     */
    public function restored(Model $model): void
    {
        $this->logEvent('restored', $model);
        $this->onRestored($model);
    }

    /**
     * Handle the Model "force deleted" event.
     */
    public function forceDeleted(Model $model): void
    {
        $this->logEvent('force deleted', $model);
        $this->onForceDeleted($model);
    }

    /**
     * Log the event
     */
    protected function logEvent(string $event, Model $model): void
    {
        $modelName = class_basename($model);
        $modelId = $model->getKey();

        Log::info("Model {$modelName} {$event}", [
            'model_id' => $modelId,
            'event' => $event,
            'user_id' => optional(auth()->user())->id ?? 'guest',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Hook for created event
     */
    protected function onCreated(Model $model): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook for updated event
     */
    protected function onUpdated(Model $model): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook for deleted event
     */
    protected function onDeleted(Model $model): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook for restored event
     */
    protected function onRestored(Model $model): void
    {
        // Override in child classes if needed
    }

    /**
     * Hook for force deleted event
     */
    protected function onForceDeleted(Model $model): void
    {
        // Override in child classes if needed
    }
}
