<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Knowledge;
use App\Observers\KnowledgeObserver;
use App\Observers\KnowledgeChangeObserver;
use App\Services\ChangeLog\ChangeLogService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register ChangeLogService as singleton
        $this->app->singleton(ChangeLogService::class, function ($app) {
            return new ChangeLogService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Knowledge::observe(KnowledgeObserver::class);
        Knowledge::observe(KnowledgeChangeObserver::class);
    }
}
