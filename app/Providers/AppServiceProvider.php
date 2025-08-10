<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Knowledge;
use App\Observers\KnowledgeObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Knowledge::observe(KnowledgeObserver::class);
    }
}
